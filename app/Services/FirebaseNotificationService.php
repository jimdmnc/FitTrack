<?php

namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    public function sendNotification(string $title, string $body, array $fcmTokens, array $data = [])
    {
        try {
            $messaging = Firebase::messaging();

            $message = CloudMessage::new()
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ])
                ->withData($data)
                ->withTarget('token', $fcmTokens);

            $response = $messaging->send($message);

            Log::info('Firebase notification sent successfully', [
                'tokens' => $fcmTokens,
                'response' => $response
            ]);

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