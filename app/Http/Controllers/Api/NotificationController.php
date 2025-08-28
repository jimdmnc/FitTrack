<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseNotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        try {
            // Validate the incoming id
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:users,id', // Use 'id' instead of 'user_id'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for sendNotification: ' . $validator->errors()->first());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the user by id
            $id = $request->input('id');
            $user = User::find($id);

            if (!$user) {
                Log::error('User not found in sendNotification', ['id' => $id]);
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            if (!$user->fcm_token) {
                Log::error('FCM token not found for user', ['user_id' => $id]);
                return response()->json(['success' => false, 'message' => 'FCM token not found'], 404);
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
                Log::info('Notifications sent successfully', ['user_id' => $id, 'details' => $sentMessages]);
                return response()->json([
                    'success' => true,
                    'message' => 'Notifications sent',
                    'details' => $sentMessages
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to send notifications'], 500);
        } catch (\Exception $e) {
            Log::error('Error sending notifications: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications',
                'error' => $e->getMessage()
            ], 500);
        }
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
        if (strtolower($user->member_status) === 'expired') { // Changed to member_status to match your table
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

        // Add daily calorie reminder (temporary placeholder for session logic)
        // Note: Session approach may need adjustment for Hostinger
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