<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class UpdateMemberStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired member statuses and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Initialize Firebase service
        $firebaseService = new FirebaseNotificationService();

        // Step 1: Update expired memberships
        $expiredUsers = User::where('end_date', '<', Carbon::today())
            ->where('member_status', 'Active')
            ->get();

        foreach ($expiredUsers as $user) {
            $user->update(['member_status' => 'Expired']);

            $fcmTokens = $user->fcm_token ? [$user->fcm_token] : [];
            if (!empty($fcmTokens)) {
                $title = 'Membership Expired';
                $body = 'Your membership has expired! Please renew.';
                $data = ['time' => now()->toDateTimeString()];
                $result = $firebaseService->sendNotification($title, $body, $fcmTokens, $data);
                Log::info('Sent expiration notification to user ' . $user->id, ['result' => $result]);
            } else {
                Log::warning('No FCM token for expired user ID: ' . $user->id);
            }
        }

        // Step 2: Check for memberships expiring soon (within 3 days)
        $expiringSoonUsers = User::where('end_date', '>=', Carbon::today())
            ->where('end_date', '<=', Carbon::today()->addDays(3))
            ->where('member_status', 'Active')
            ->get();

        foreach ($expiringSoonUsers as $user) {
            $fcmTokens = $user->fcm_token ? [$user->fcm_token] : [];
            if (!empty($fcmTokens)) {
                $endDate = Carbon::parse($user->end_date);
                $daysLeft = Carbon::today()->diffInDays($endDate, false);
                $title = 'Membership Expiring Soon';
                $body = 'Your membership will expire in ' . $daysLeft . ' day(s)!';
                $data = ['time' => now()->toDateTimeString()];
                $result = $firebaseService->sendNotification($title, $body, $fcmTokens, $data);
                Log::info('Sent expiration warning to user ' . $user->id, ['result' => $result]);
            } else {
                Log::warning('No FCM token for expiring user ID: ' . $user->id);
            }
        }

        // Step 3: Check for daily calorie reminder
        $users = User::all(); // Or filter active users
        foreach ($users as $user) {
            $fcmTokens = $user->fcm_token ? [$user->fcm_token] : [];
            if (!empty($fcmTokens)) {
                $today = Carbon::today()->dayOfYear;
                $prefs = json_decode($user->preferences ?? '{}', true); // Assuming preferences stores reminder state
                $lastShownDay = $prefs['last_reminder_day'] ?? -1;

                if ($today != $lastShownDay) {
                    $title = 'Daily Food Reminder';
                    $body = "Don't forget to log your meal today!";
                    $data = ['time' => now()->toDateTimeString()];
                    $result = $firebaseService->sendNotification($title, $body, $fcmTokens, $data);
                    Log::info('Sent daily reminder to user ' . $user->id, ['result' => $result]);

                    // Update last shown day
                    $prefs['last_reminder_day'] = $today;
                    $user->update(['preferences' => json_encode($prefs)]);
                }
            }
        }

        $this->info('Member status updates and notifications completed.');
        return 0;
    }
}