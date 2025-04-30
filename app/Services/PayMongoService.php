<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Models\User; 
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

    /**
     * Create a GCash payment source
     */
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
            
            Log::info('GCash source created', [
                'source_id' => $sourceData['id'],
                'checkout_url' => $sourceData['attributes']['redirect']['checkout_url'] ?? 'unknown',
                'test_mode' => $this->isTestMode
            ]);

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

    /**
     * Create a payment using a chargeable source
     */
    public function createPayment(string $sourceId)
    {
        try {
            // First verify the source is chargeable
            $source = $this->verifyPayment($sourceId);
            
            if ($source['attributes']['status'] !== 'chargeable') {
                throw new \Exception("Source is not in chargeable state: " . $source['attributes']['status']);
            }

            // Create the payment
            $response = $this->client->post('payments', [
                'headers' => $this->getHeaders(),
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $source['attributes']['amount'],
                            'currency' => $source['attributes']['currency'],
                            'source' => [
                                'id' => $sourceId,
                                'type' => 'source'
                            ],
                            'description' => $source['attributes']['description'] ?? 'Payment for source: ' . $sourceId,
                            'metadata' => $source['attributes']['metadata'] ?? [],
                        ],
                    ],
                ],
            ]);

            $paymentData = $this->parseResponse($response);
            
            Log::info('Payment created successfully', [
                'source_id' => $sourceId,
                'payment_id' => $paymentData['id'],
                'status' => $paymentData['attributes']['status'] ?? 'unknown',
                'test_mode' => $this->isTestMode
            ]);
            
            return $paymentData;
        } catch (GuzzleException $e) {
            $this->logError('Payment creation failed', $e, [
                'source_id' => $sourceId,
            ]);
            throw new \Exception("Payment creation failed: " . $e->getMessage());
        }
    }

    /**
     * Verify the payment source status
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
                
                Log::info('Source verification', [
                    'source_id' => $sourceId,
                    'status' => $data['attributes']['status'] ?? 'unknown',
                    'test_mode' => $this->isTestMode
                ]);

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

    /**
     * Retrieve payment details
     */
    public function retrievePayment(string $paymentId)
    {
        try {
            $response = $this->client->get("payments/{$paymentId}", [
                'headers' => $this->getHeaders(),
            ]);

            return $this->parseResponse($response);
        } catch (GuzzleException $e) {
            $this->logError('Payment retrieval failed', $e, [
                'payment_id' => $paymentId,
            ]);
            throw new \Exception("Payment retrieval failed: " . $e->getMessage());
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
        return route('payment.' . $type);
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
}