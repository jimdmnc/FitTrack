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
        $this->client = new Client(['base_uri' => 'https://api.paymongo.com/v1/']);
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
                            'amount' => $amount * 100, // Convert to centavos
                            'redirect' => [
                                'success' => config('app.url') . '/payment/success',
                                'failed' => config('app.url') . '/payment/failed',
                            ],
                            'type' => 'gcash',
                            'currency' => 'PHP',
                            'description' => $description,
                            'metadata' => $metadata,
                        ],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['data'];
        } catch (GuzzleException $e) {
            Log::error('PayMongo GCash source creation failed: ' . $e->getMessage());
            throw new \Exception('Payment gateway error');
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(string $sourceId)
    {
        try {
            $response = $this->client->get("sources/{$sourceId}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['data'];
        } catch (GuzzleException $e) {
            Log::error('PayMongo payment verification failed: ' . $e->getMessage());
            throw new \Exception('Payment verification error');
        }
    }
}