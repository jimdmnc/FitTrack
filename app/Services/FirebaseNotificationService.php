<?php

namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    public function sendNotification(string $title, string $body, array $fcmTokens, array $data = [])
    {
        try {
            $messaging = Firebase::messaging();

            foreach ($fcmTokens as $token) {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($data);

                $response = $messaging->send($message);

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
