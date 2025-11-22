<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Renewal;
use App\Models\MembersPayment;
use App\Models\Price;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
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

            $query->orderBy('end_date', 'asc');
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






    public function upgradeMembership(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string', // This is the NEW RFID from auto-fetch
            'old_rfid_uid' => 'required|exists:users,rfid_uid', // The current user in the system
            'membership_type' => 'required|in:custom,7,30,365',
            'custom_days' => 'required_if:membership_type,custom|nullable|integer|min:1|max:365',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ], [
            'old_rfid_uid.exists' => 'The selected member could not be found.',
            'rfid_uid.required' => 'Please tap a new RFID card.',
            'membership_type.in' => 'Please select a valid membership type.',
            'custom_days.required_if' => 'Please specify the number of days for custom membership.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.after' => 'End date must be after the start date.',
        ]);
    
        try {
            // Find the user using the OLD RFID (the one currently in the system)
            $user = User::where('rfid_uid', $request->old_rfid_uid)->firstOrFail();
    
            // The new RFID from auto-fetch
            $newRfidUid = strtoupper(trim($request->rfid_uid));
    
            // Optional: Prevent assigning the same RFID twice
            if ($user->rfid_uid === $newRfidUid) {
                return back()->with('info', 'Same RFID card detected. No changes made.');
            }
    
            // Optional: Prevent duplicate RFID assignment
            $existingUser = User::where('rfid_uid', $newRfidUid)->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                return back()->with('error', "This RFID card ($newRfidUid) is already assigned to another member.");
            }
    
            // Fetch prices
            $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])
                ->pluck('amount', 'type');
    
            $priceType = match ($request->membership_type) {
                'custom' => 'session',
                '7'      => 'weekly',
                '30'     => 'monthly',
                '365'    => 'annual',
            };
    
            $baseAmount = $prices[$priceType] ?? throw new \Exception("Price not found for $priceType");
    
            $membershipDays = $request->membership_type === 'custom'
                ? (int) $request->custom_days
                : (int) $request->membership_type;
    
            $paymentAmount = $request->membership_type === 'custom'
                ? $membershipDays * $baseAmount
                : $baseAmount;
    
            DB::transaction(function () use ($user, $request, $newRfidUid, $paymentAmount, $membershipDays) {
                // 1. Update the user's RFID + membership details
                $user->update([
                    'rfid_uid'       => $newRfidUid,           // ← This is the key change!
                    'membership_type'=> $request->membership_type,
                    'start_date'     => $request->start_date,
                    'end_date'       => $request->end_date,
                    'member_status'  => 'active',
                ]);
    
                // 2. Record the renewal (with NEW RFID)
                Renewal::create([
                    'rfid_uid'       => $newRfidUid,
                    'membership_type'=> $request->membership_type,
                    'start_date'     => $request->start_date,
                    'end_date'       => $request->end_date,
                    'days'           => $membershipDays,
                    'amount'         => $paymentAmount,
                    'status'         => 'approved', // since staff is doing it directly
                ]);
    
                // 3. Record payment
                MembersPayment::create([
                    'rfid_uid'       => $newRfidUid,
                    'amount'         => $paymentAmount,
                    'payment_method' => 'cash',
                    'payment_date'   => now(),
                ]);
    
                // Optional: Clear the temporary RFID from your rfid_tags table
                // (so it doesn't get reused accidentally)
                \DB::table('rfid_tags')->where('uid', $newRfidUid)->delete();
            });
    
            return redirect()->route('staff.viewmembers')
                ->with('success', "Membership upgraded successfully! New RFID: {$newRfidUid}");
                
        } catch (\Exception $e) {
            Log::error('Upgrade membership failed: ' . $e->getMessage(), $request->all());
            return back()->with('error', 'Failed to upgrade membership: ' . $e->getMessage());
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