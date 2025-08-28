<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseNotificationService;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user || !$user->fcm_token) {
            return response()->json(['success' => false, 'message' => 'User or token not found'], 404);
        }

        $notifications = $this->getNotificationItems($user);
        $firebaseService = new FirebaseNotificationService();
        $success = true;
        $sentMessages = [];

        foreach ($notifications as $notification) {
            $result = $firebaseService->sendNotification(
                $notification['title'],
                $notification['body'],
                [$user->fcm_token],
                ['time' => $notification['time']] // Optional data
            );
            $success = $success && $result;
            $sentMessages[] = "Sent: {$notification['title']} - {$notification['body']}";
        }

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notifications sent',
                'details' => $sentMessages
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send notifications'], 500);
    }

    private function getNotificationItems($user)
    {
        $notifications = [];
        $timeFormat = new \DateTime();
        $currentTime = $timeFormat->format('h:i A');

        // Add membership notification if exists
        if ($user->membership_notification) {
            $notifications[] = [
                'title' => 'Membership Update',
                'body' => $user->membership_notification,
                'time' => $currentTime
            ];
        }

        // Add status notification ONLY IF EXPIRED
        if (strtolower($user->membership_status) === 'expired') {
            $notifications[] = [
                'title' => 'Membership Status',
                'body' => 'Your membership has expired! Please renew.',
                'time' => $currentTime
            ];
        }

        // Add expiring notification if needed (3 days or less remaining)
        if ($user->end_date) {
            $endDate = new \DateTime($user->end_date);
            $today = new \DateTime();
            $diff = $today->diff($endDate)->days;
            $daysLeft = $diff >= 0 ? $diff : 0; // Handle past dates

            if ($daysLeft <= 3 && $daysLeft >= 0) {
                $notifications[] = [
                    'title' => 'Expiring Soon',
                    'body' => "Membership will expire in {$daysLeft} day(s)!",
                    'time' => $currentTime
                ];
            }
        }

        // Add daily calorie reminder
        $prefs = app('session')->get('CalorieReminderPrefs', ['lastShownDay' => -1]);
        $today = (new \DateTime())->format('z'); // Day of year
        $lastShownDay = $prefs['lastShownDay'] ?? -1;

        if ($today == $lastShownDay) {
            $notifications[] = [
                'title' => 'Daily Food Reminder',
                'body' => "Don't forget to log your meal today!",
                'time' => '6:00 AM'
            ];
        }

        // Add default if no notifications
        if (empty($notifications)) {
            $notifications[] = [
                'title' => 'No Notifications',
                'body' => 'No new notifications',
                'time' => $currentTime
            ];
        }

        return $notifications;
    }
}