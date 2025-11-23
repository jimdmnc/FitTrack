<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Renewal;
use App\Models\MembersPayment;
use App\Models\Price;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\RfidTag;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViewmembersController extends Controller
{
    /**
     * Check and update member status based on expiration date
     *
     * @param User $member
     * @return void
     */
    private function updateMemberStatus(User $member)
    {
        $currentDate = Carbon::now();

        if ($member->end_date && Carbon::parse($member->end_date)->lt($currentDate)) {
            if (!in_array($member->member_status, ['expired', 'revoked'])) {
                $member->member_status = 'expired';
                $member->save();
            }
        } elseif ($member->end_date && Carbon::parse($member->end_date)->gt($currentDate)) {
            if ($member->member_status === 'expired') {
                $member->member_status = 'active';
                $member->save();
            }
        }
    }

    /**
     * Display the members list with search and status filters
     */
    public function index(Request $request)
    {
        try {
            $searchQuery = $request->input('search');
            $status = $request->input('status', 'all');

            // Update status for all relevant members
            $allMembers = User::whereIn('role', ['user', 'userSession'])->get();
            foreach ($allMembers as $member) {
                $this->updateMemberStatus($member);
            }

            // Build query
            $query = User::whereIn('role', ['user', 'userSession']);

            if ($status !== 'all') {
                $query->where('member_status', $status);
            }

            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('first_name', 'like', "%{$searchQuery}%")
                      ->orWhere('last_name', 'like', "%{$searchQuery}%")
                      ->orWhere('rfid_uid', 'like', "%{$searchQuery}%");
                });
            }

            // Order by: active first (most recent registered), then expired (most recent), then revoked (most recent)
            // Primary sort: status priority (active=1, expired=2, revoked=3)
            // Secondary sort: most recently started/registered first (by start_date DESC)
            $query->orderByRaw("CASE 
                WHEN member_status = 'active' THEN 1 
                WHEN member_status = 'expired' THEN 2 
                WHEN member_status = 'revoked' THEN 3 
                ELSE 4 
            END")
            ->orderBy('start_date', 'desc') // Sort by issued/start date (most recent first)
            ->orderBy('created_at', 'desc') // Then by creation date as secondary sort
            ->orderBy('id', 'desc'); // Additional sort by ID for consistency (newest first)
            
            $members = $query->paginate(10)->appends($request->all());

            if ($request->ajax()) {
                try {
                    $tableView = view('partials.members_table', [
                        'members' => $members
                    ])->render();
                    $paginationView = $members->links()->render();
                    return response()->json([
                        'table' => $tableView,
                        'pagination' => $paginationView,
                        'currentPage' => $members->currentPage(),
                        'lastPage' => $members->lastPage(),
                        'total' => $members->total()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error rendering members table: ' . $e->getMessage());
                    return response()->json([
                        'table' => '<div class="text-center py-8 text-gray-500"><p>Unable to load members data. Please try again later.</p></div>',
                        'pagination' => '',
                        'error' => true
                    ], 500);
                }
            }

            return view('staff.viewmembers', [
                'members' => $members,
                'query' => $searchQuery,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Member listing error: ' . $e->getMessage());
            $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('staff.viewmembers', [
                'members' => $members,
                'error' => 'Unable to load members. Please try again later.',
                'query' => $searchQuery,
                'status' => $status
            ]);
        }
    }

    /**
     * Return membership prices for AJAX requests
     */
    public function membershipPrices(Request $request)
    {
        try {
            $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])
                ->get()
                ->keyBy('type')
                ->mapWithKeys(function ($price) {
                    return [$price->type => floatval($price->amount)];
                })->toArray();

            return response()->json([
                'session' => $prices['session'] ?? 0,
                'weekly' => $prices['weekly'] ?? 0,
                'monthly' => $prices['monthly'] ?? 0,
                'annual' => $prices['annual'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching membership prices: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load membership prices'], 500);
        }
    }


/**
 * Upgrade a session member to RFID card membership
 */
/**
 * Upgrade a session member to RFID card membership
 */
/**
 * Upgrade a session member to RFID card membership
 */
/**
 * Upgrade a session member to RFID card membership
 */
public function upgradeMembership(Request $request)
{
    $request->validate([
        'member_id' => 'required|exists:users,id',
        'current_rfid_uid' => 'required|exists:users,rfid_uid',
        'uid' => 'required|string|unique:users,rfid_uid',
        'membership_type' => 'required|in:7,30,365',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
    ], [
        'member_id.exists' => 'The selected member is invalid.',
        'current_rfid_uid.exists' => 'The current member ID is invalid.',
        'uid.required' => 'Please tap an RFID card to continue.',
        'uid.unique' => 'This RFID card is already registered to another member.',
        'membership_type.in' => 'Please select a valid membership type.',
        'start_date.after_or_equal' => 'The start date cannot be in the past.',
        'end_date.after' => 'The expiration date must be after the start date.',
    ]);

    try {
        // Get the member by ID
        $user = User::findOrFail($request->member_id);

        // Verify the current RFID UID matches
        if ($user->rfid_uid !== $request->current_rfid_uid) {
            throw new \Exception('Member ID mismatch. Please refresh and try again.');
        }

        // Get membership price
        $prices = Price::whereIn('type', ['weekly', 'monthly', 'annual'])
            ->get()
            ->keyBy('type');

        $requiredPriceType = match ($request->membership_type) {
            '7' => 'weekly',
            '30' => 'monthly',
            '365' => 'annual',
            default => throw new \Exception('Invalid membership type selected.'),
        };

        if (!$prices->has($requiredPriceType)) {
            throw new \Exception("Price for {$requiredPriceType} membership is not configured.");
        }

        $price = $prices[$requiredPriceType];
        $membershipDays = (int) $request->membership_type;
        $paymentAmount = $price->amount;

        $oldRfidUid = $user->rfid_uid;
        $newRfidUid = $request->uid;

        DB::transaction(function () use ($user, $request, $paymentAmount, $membershipDays, $oldRfidUid, $newRfidUid) {
            
            // Step 1: Temporarily disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            try {
                // Step 2: Update the user's RFID UID and membership details
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'rfid_uid' => $newRfidUid,
                        'role' => 'user',
                        'membership_type' => $request->membership_type,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'member_status' => 'active',
                        'updated_at' => now(),
                    ]);

                // Step 3: Update all related tables that reference the old rfid_uid
                
                // Update attendances table
                DB::table('attendances')
                    ->where('rfid_uid', $oldRfidUid)
                    ->update(['rfid_uid' => $newRfidUid]);
                
                // Update renewals table (if exists and has records)
                if (DB::table('renewals')->where('rfid_uid', $oldRfidUid)->exists()) {
                    DB::table('renewals')
                        ->where('rfid_uid', $oldRfidUid)
                        ->update(['rfid_uid' => $newRfidUid]);
                }
                
                // Update members_payments table (if exists and has records)
                if (DB::table('members_payments')->where('rfid_uid', $oldRfidUid)->exists()) {
                    DB::table('members_payments')
                        ->where('rfid_uid', $oldRfidUid)
                        ->update(['rfid_uid' => $newRfidUid]);
                }

                // Step 4: Mark the new RFID tag as registered
                DB::table('rfid_tags')
                    ->where('uid', $newRfidUid)
                    ->update([
                        'registered' => 1, // or true, depending on your column type
                        'updated_at' => now(),
                    ]);

                // Optional: Mark the old RFID tag as unregistered if needed
                DB::table('rfid_tags')
                    ->where('uid', $oldRfidUid)
                    ->update([
                        'registered' => 0, // or false
                        'updated_at' => now(),
                    ]);

            } finally {
                // Step 5: Always re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // Step 6: Create new renewal record with new RFID UID
            Renewal::create([
                'rfid_uid' => $newRfidUid,
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => $membershipDays,
                'amount' => $paymentAmount,
            ]);

            // Step 7: Create payment record with new RFID UID
            MembersPayment::create([
                'rfid_uid' => $newRfidUid,
                'amount' => $paymentAmount,
                'payment_method' => 'cash',
                'payment_date' => now(),
            ]);
        });

        return redirect()->route('staff.viewmembers')
            ->with('success', 'Member upgraded to RFID card membership successfully! New RFID: ' . $newRfidUid);
    } catch (\Exception $e) {
        Log::error('Membership upgrade error: ' . $e->getMessage(), [
            'member_id' => $request->member_id,
            'old_rfid' => $request->current_rfid_uid ?? 'N/A',
            'new_rfid' => $request->uid ?? 'N/A',
        ]);
        return redirect()->route('staff.viewmembers')
            ->with('error', 'Failed to upgrade membership: ' . $e->getMessage());
    }
}




    /**
     * Handle membership renewal
     */
    public function renewMembership(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'membership_type' => 'required|in:custom,7,30,365',
            'custom_days' => 'required_if:membership_type,custom|nullable|integer|min:1|max:365',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ], [
            'rfid_uid.exists' => 'The selected member ID is invalid.',
            'membership_type.in' => 'Please select a valid membership type.',
            'custom_days.required_if' => 'Please specify the number of days for a custom membership.',
            'custom_days.integer' => 'The number of days must be a valid integer.',
            'custom_days.min' => 'The number of days must be at least 1.',
            'custom_days.max' => 'The number of days cannot exceed 365.',
            'start_date.after_or_equal' => 'The renewal date cannot be in the past.',
            'end_date.after' => 'The expiration date must be after the renewal date.',
        ]);

        try {
            $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

            $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])
                ->get()
                ->keyBy('type');

            $requiredPriceType = match ($request->membership_type) {
                'custom' => 'session',
                '7' => 'weekly',
                '30' => 'monthly',
                '365' => 'annual',
                default => throw new \Exception('Invalid membership type selected.'),
            };

            if (!$prices->has($requiredPriceType)) {
                throw new \Exception("Price for {$requiredPriceType} membership is not configured.");
            }

            $price = $prices[$requiredPriceType];

            $membershipDays = match ($request->membership_type) {
                'custom' => (int) $request->custom_days,
                '7' => 7,
                '30' => 30,
                '365' => 365,
                default => throw new \Exception('Invalid membership type selected.'),
            };

            $paymentAmount = match ($request->membership_type) {
                'custom' => (int) $request->custom_days * $price->amount,
                '7', '30', '365' => $price->amount,
                default => throw new \Exception('Invalid membership type selected.'),
            };

            DB::transaction(function () use ($user, $request, $paymentAmount, $membershipDays) {
                $user->update([
                    'membership_type' => $request->membership_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'member_status' => 'active',
                ]);

                Renewal::create([
                    'rfid_uid' => $user->rfid_uid,
                    'membership_type' => $request->membership_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'days' => $membershipDays,
                    'amount' => $paymentAmount,
                ]);

                MembersPayment::create([
                    'rfid_uid' => $user->rfid_uid,
                    'amount' => $paymentAmount,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);
            });

            return redirect()->route('staff.viewmembers')
                ->with('success', 'Member renewed successfully!');
        } catch (\Exception $e) {
            Log::error('Membership renewal error: ' . $e->getMessage());
            return redirect()->route('staff.viewmembers')
                ->with('error', 'Failed to renew membership: ' . $e->getMessage());
        }
    }

    /**
     * Revoke a member's access
     */
    public function revokeMember(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'reason' => 'nullable|string|max:255',
        ], [
            'rfid_uid.exists' => 'The selected member ID is invalid.',
        ]);

        try {
            $member = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

            if ($member->member_status === 'revoked') {
                return redirect()->back()->with('error', 'This member is already revoked.');
            }

            $member->update([
                'member_status' => 'revoked',
                'revoke_reason' => $request->reason,
                'revoked_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Member revoked successfully.');
        } catch (\Exception $e) {
            Log::error('Member revocation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to revoke member: ' . $e->getMessage());
        }
    }

    /**
     * Restore a revoked member
     */
    public function restoreMember(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
        ], [
            'rfid_uid.exists' => 'The selected member ID is invalid.',
        ]);

        try {
            $member = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

            if ($member->member_status !== 'revoked') {
                return redirect()->back()->with('error', 'This member is not revoked.');
            }

            $currentDate = Carbon::now();
            $member->member_status = $member->end_date && Carbon::parse($member->end_date)->lt($currentDate)
                ? 'expired'
                : 'active';

            $member->revoke_reason = null;
            $member->revoked_at = null;
            $member->save();

            return redirect()->back()->with('success', 'Member restored successfully.');
        } catch (\Exception $e) {
            Log::error('Member restoration error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore member: ' . $e->getMessage());
        }
    }
}