<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

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

    /**
     * Create a GCash payment source
     */
    public function createGcashSource(float $amount, string $description, array $metadata = [])
    {
        try {
            // Flatten metadata to prevent nesting
            $flatMetadata = $this->flattenMetadata($metadata);

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
                            'metadata' => $this->enrichMetadata($flatMetadata),
                        ],
                    ],
                ],
            ]);

            return $this->parseResponse($response);
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

    /**
     * Verify payment status with retry logic
     */
    public function verifyPayment(string $sourceId, int $maxRetries = 2)
    {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->get("sources/{$sourceId}", [
                    'headers' => $this->getHeaders(),
                ]);

                $data = $this->parseResponse($response);
                
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

    // ===== Helper Methods =====
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

    /**
     * Flatten nested metadata arrays
     */
    private function flattenMetadata(array $metadata): array
    {
        $flat = [];
        foreach ($metadata as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $flat["{$key}_{$subKey}"] = is_array($subValue) ? json_encode($subValue) : $subValue;
                }
            } else {
                $flat[$key] = $value;
            }
        }
        return $flat;
    }

    private function enrichMetadata(array $metadata): array
    {
        return array_merge($metadata, [
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
}