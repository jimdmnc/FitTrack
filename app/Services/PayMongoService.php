<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Make sure to import your User model
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
                                'success' => $this->getRedirectUrl('successs'),
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

            // For test mode, automatically verify and activate with all required fields
            if ($this->isTestMode) {
                $this->handleTestPaymentActivation($sourceData['id'], $metadata);
            }

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

    public function verifyPayment(string $sourceId, array $metadata = [], int $maxRetries = 2)
    {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->get("sources/{$sourceId}", [
                    'headers' => $this->getHeaders(),
                ]);

                $data = $this->parseResponse($response);
                
                if ($this->isTestMode && $data['attributes']['status'] === 'pending') {
                    $data['attributes']['status'] = 'expired';
                    $this->activateUserMembership($sourceId, $metadata);
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

    private function handleTestPaymentActivation(string $sourceId, array $metadata)
    {
        try {
            // Verify the test payment immediately
            $verifiedData = $this->verifyPayment($sourceId, $metadata, 1);
            
            if ($verifiedData['attributes']['status'] === 'expired') {
                $this->activateUserMembership($sourceId, $metadata);
            }
        } catch (\Exception $e) {
            Log::error('Test payment activation failed', [
                'source_id' => $sourceId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function activateUserMembership(string $sourceId, array $metadata)
    {
        if (isset($metadata['user_id']) || isset($metadata['rfid_uid'])) {
            $userId = $metadata['user_id'] ?? null;
            $rfidUid = $metadata['rfid_uid'] ?? null;
            
            $user = $userId 
                ? User::find($userId)
                : User::where('rfid_uid', $rfidUid)->first();

            if ($user) {
                // Calculate dates based on membership type if not provided
                $startDate = $metadata['start_date'] ?? now()->toDateString();
                $endDate = $metadata['end_date'] ?? $this->calculateEndDate(
                    $metadata['membership_type'] ?? '7', // default 7 days
                    $startDate
                );

                $updateData = [
                    'member_status' => 'active',
                    'session_status' => 'approved',
                    'needs_approval' => 0,
                    'membership_type' => $metadata['membership_type'] ?? $user->membership_type,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];

                $user->update($updateData);

                Log::info('Membership fully activated for user', [
                    'user_id' => $user->id,
                    'rfid_uid' => $user->rfid_uid,
                    'source_id' => $sourceId,
                    'update_data' => $updateData,
                    'test_mode' => $this->isTestMode
                ]);
            }
        }
    }


    private function calculateEndDate(string $membershipType, string $startDate): string
    {
        $days = (int)$membershipType;
        return Carbon::parse($startDate)->addDays($days)->toDateString();
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
}