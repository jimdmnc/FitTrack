<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    private $messaging;

    public function __construct()
    {
        // Point to your Firebase service account JSON key
        $serviceAccountPath = base_path('firebase-key.json'); 
        // Upload your service account file to Laravel root folder and rename it firebase-key.json

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification(string $title, string $body, array $fcmTokens, array $data = [])
    {
        try {
            foreach ($fcmTokens as $token) {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification([
                        'title' => $title,
                        'body' => $body,
                    ])
                    ->withData($data);

                $response = $this->messaging->send($message);

                Log::info('Firebase notification sent successfully', [
                    'token' => $token,
                    'response' => $response
                ]);
            }

            return true;
        } catch (MessagingException $e) {
            Log::error('Firebase notification failed: ' . $e->getMessage(), [
                'tokens' => $fcmTokens,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
