<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private $client;
    private $secretKey;
    private $publicKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.paymongo.com/v1/',
            'timeout' => 30, // Add timeout
        ]);
        $this->secretKey = config('services.paymongo.secret_key');
        $this->publicKey = config('services.paymongo.public_key');
    }

    /**
     * Create a GCash payment source
     */
    public function createGcashSource(float $amount, string $description, array $metadata = [])
    {
        try {
            $response = $this->client->post('sources', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                ],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $this->convertToCentavos($amount),
                            'redirect' => [
                                'success' => route('payment.success'), // Use named routes
                                'failed' => route('payment.failed'),
                            ],
                            'type' => 'gcash',
                            'currency' => 'PHP',
                            'description' => $description,
                            'metadata' => array_merge($metadata, [
                                'system' => 'FitTrack Membership',
                                'environment' => config('app.env'), // Track test/prod
                            ]),
                        ],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            
            if (!isset($body['data'])) {
                throw new \Exception('Invalid PayMongo response format');
            }

            return $body['data'];
        } catch (GuzzleException $e) {
            Log::error('PayMongo GCash source creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'description' => $description,
            ]);
            throw new \Exception('Payment gateway error: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment status with retry logic
     */
    public function verifyPayment(string $sourceId, int $maxRetries = 3)
    {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->get("sources/{$sourceId}", [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                    ],
                ]);

                $body = json_decode($response->getBody(), true);
                
                if (!isset($body['data'])) {
                    throw new \Exception('Invalid PayMongo response format');
                }

                return $body['data'];
            } catch (GuzzleException $e) {
                $retryCount++;
                if ($retryCount >= $maxRetries) {
                    Log::error('PayMongo payment verification failed after retries', [
                        'source_id' => $sourceId,
                        'error' => $e->getMessage(),
                        'attempt' => $retryCount,
                    ]);
                    throw new \Exception('Payment verification error: ' . $e->getMessage());
                }
                sleep(1); // Wait before retrying
            }
        }
    }

    /**
     * Convert amount to centavos (smallest currency unit)
     */
    private function convertToCentavos(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Create a payment intent (optional - for more complex flows)
     */
    public function createPaymentIntent(float $amount, string $description, array $metadata = [])
    {
        try {
            $response = $this->client->post('payment_intents', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                ],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $this->convertToCentavos($amount),
                            'payment_method_allowed' => ['gcash'],
                            'payment_method_options' => [
                                'card' => [
                                    'request_three_d_secure' => 'any',
                                ],
                            ],
                            'currency' => 'PHP',
                            'description' => $description,
                            'metadata' => $metadata,
                        ],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            
            if (!isset($body['data'])) {
                throw new \Exception('Invalid PayMongo response format');
            }

            return $body['data'];
        } catch (GuzzleException $e) {
            Log::error('PayMongo payment intent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
            ]);
            throw new \Exception('Payment intent creation error: ' . $e->getMessage());
        }
    }
}