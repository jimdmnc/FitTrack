<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    private $credentialsPath = '/home/u584578286/domains/rockiesfitnessph.com/private/firebase-key.json'; 
    private $projectId = 'fittrackapp-cfbae'; // Replace with your Firebase project ID

    public function sendNotification(string $title, string $body, array $fcmTokens, array $data = [])
    {
        try {
            // Load service account key
            $client = new Client();
            $client->setAuthConfig($this->credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            foreach ($fcmTokens as $token) {
                $message = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => $data,
                    ]
                ];

                $headers = [
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: application/json"
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200) {
                    Log::error("FCM v1 failed: HTTP {$httpCode}", ['response' => $result]);
                } else {
                    Log::info("FCM v1 success", ['response' => $result]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Firebase v1 send error: " . $e->getMessage());
        }
    }
}
