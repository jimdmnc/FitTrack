<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayMongoService
{
    private $client;
    private $secretKey;
    private $isTestMode;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.paymongo.com/v1/',
            'timeout' => 15,
        ]);
        
        $this->secretKey = config('services.paymongo.secret_key');
        $this->isTestMode = str_starts_with($this->secretKey, 'sk_test_');
    }

    public function createGcashSource(float $amount, string $description, array $metadata = [])
    {
        try {
            $response = $this->client->post('sources', [
                'headers' => $this->getHeaders(),
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $this->convertToCentavos($amount),
                            'redirect' => [
                                'success' => $this->getRedirectUrl('success'),
                                'failed' => $this->getRedirectUrl('failed'),
                            ],
                            'type' => 'gcash',
                            'currency' => 'PHP',
                            'description' => $description,
                            'metadata' => $this->prepareMetadata($metadata),
                        ],
                    ],
                ],
            ]);

            $sourceData = $this->parseResponse($response);
            return $sourceData;
        } catch (GuzzleException $e) {
            $this->logError('GCash source creation failed', $e, [
                'amount' => $amount,
                'description' => $description,
            ]);
            throw new \Exception($this->isTestMode 
                ? "Test payment error: " . $e->getMessage()
                : "Payment processing failed");
        }
    }

    public function verifyPayment(string $sourceId, int $maxRetries = 2)
    {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->get("sources/{$sourceId}", [
                    'headers' => $this->getHeaders(),
                ]);

                $data = $this->parseResponse($response);
                
                // For test mode, automatically set status to chargeable
                if ($this->isTestMode && $data['attributes']['status'] === 'pending') {
                    $data['attributes']['status'] = 'chargeable';
                }

                return $data;
            } catch (GuzzleException $e) {
                $retryCount++;
                if ($retryCount >= $maxRetries) {
                    $this->logError('Payment verification failed', $e, [
                        'source_id' => $sourceId,
                        'attempts' => $retryCount,
                    ]);
                    throw new \Exception("Payment verification timeout");
                }
                usleep(500000);
            }
        }
    }

    private function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
        ];
    }

    private function getRedirectUrl(string $type): string
    {
        return $this->isTestMode
            ? 'https://rockiesfitnessph.com/payment/' . $type
            : route('payment.' . $type);
    }

    private function prepareMetadata(array $metadata): array
    {
        // Convert all values to strings and ensure no nesting
        $prepared = [];
        foreach ($metadata as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $prepared[$key] = json_encode($value);
            } else {
                $prepared[$key] = (string)$value;
            }
        }

        return array_merge($prepared, [
            'system' => 'FitTrack',
            'environment' => $this->isTestMode ? 'test' : 'production',
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function parseResponse($response): array
    {
        $body = json_decode($response->getBody(), true);
        
        if (!isset($body['data'])) {
            throw new \Exception('Invalid API response structure');
        }

        return $body['data'];
    }

    private function logError(string $message, \Throwable $e, array $context = [])
    {
        Log::error($message, array_merge($context, [
            'error' => $e->getMessage(),
            'test_mode' => $this->isTestMode,
        ]));
    }

    private function convertToCentavos(float $amount): int
    {
        return (int) round($amount * 100);
    }

    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }
}