<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Make sure to import the User model
use Carbon\Carbon;
use App\Models\GymEntry; // Import the GymEntry model


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $query = $request->input('search');
    
        // Fetch members with role 'user' and filter by name if a search query is provided
        $members = User::where('role', 'user')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4)
            ->withQueryString();
    
        // Calculate active members and new members data
        $activeMembersData = $this->getActiveMembersData();
        $newMembersData = $this->getNewMembersData();
        $todaysCheckInsData = $this->getTodaysCheckInsData();
        $expiringMemberships = $this->getExpiringMemberships();

    
        // Pass the members, search query, active members data, and new members data to the view
    // Pass the members, search query, and all data to the view
    return view('staff.dashboard', compact('members', 'query', 'activeMembersData', 'newMembersData', 'todaysCheckInsData', 'expiringMemberships'));   
 }
    
    private function getActiveMembersData()
    {
        // Get the current date and the start of the current week
        $now = Carbon::now();
        $startOfCurrentWeek = $now->startOfWeek()->toDateTimeString();
        $endOfCurrentWeek = $now->endOfWeek()->toDateTimeString();

        // Get the start of last week
        $startOfLastWeek = $now->copy()->subWeek()->startOfWeek()->toDateTimeString();
        $endOfLastWeek = $now->copy()->subWeek()->endOfWeek()->toDateTimeString();

        // Count active members this week
        $currentWeekActiveMembers = User::where('role', 'user')
            ->where('member_status', 'active')
            ->whereBetween('start_date', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->count();

        // Count active members last week
        $lastWeekActiveMembers = User::where('role', 'user')
            ->where('member_status', 'active')
            ->whereBetween('start_date', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($lastWeekActiveMembers > 0) {
            $percentageChange = (($currentWeekActiveMembers - $lastWeekActiveMembers) / $lastWeekActiveMembers) * 100;
        }

        // Determine the arrow indicator
        $arrowIndicator = ($percentageChange >= 0) ? '▲' : '▼';

        // Format the percentage change
        $formattedPercentageChange = abs(round($percentageChange, 2)) . '% vs Last Week ' . $arrowIndicator;

        return [
            'currentWeekActiveMembers' => $currentWeekActiveMembers,
            'formattedPercentageChange' => $formattedPercentageChange,
        ];
    }

    private function getNewMembersData()
    {
        // Get the current date and the start of the current week
        $now = Carbon::now();
        $startOfCurrentWeek = $now->startOfWeek()->toDateTimeString();
        $endOfCurrentWeek = $now->endOfWeek()->toDateTimeString();

        // Get the start of last week
        $startOfLastWeek = $now->copy()->subWeek()->startOfWeek()->toDateTimeString();
        $endOfLastWeek = $now->copy()->subWeek()->endOfWeek()->toDateTimeString();

        // Count new members this week (based on registration date)
        $currentWeekNewMembers = User::where('role', 'user')
            ->whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->count();

        // Count new members last week (based on registration date)
        $lastWeekNewMembers = User::where('role', 'user')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($lastWeekNewMembers > 0) {
            $percentageChange = (($currentWeekNewMembers - $lastWeekNewMembers) / $lastWeekNewMembers) * 100;
        }

        // Determine the arrow indicator
        $arrowIndicator = ($percentageChange >= 0) ? '▲' : '▼';

        // Format the percentage change
        $formattedPercentageChange = abs(round($percentageChange, 2)) . '% vs Last Week ' . $arrowIndicator;

        return [
            'currentWeekNewMembers' => $currentWeekNewMembers,
            'formattedPercentageChange' => $formattedPercentageChange,
        ];
    }

    private function getTodaysCheckInsData()
    {
        // Get the current date and the start of today
        $now = Carbon::now();
        $startOfToday = $now->startOfDay()->toDateTimeString();
        $endOfToday = $now->endOfDay()->toDateTimeString();

        // Get the start and end of yesterday
        $startOfYesterday = $now->copy()->subDay()->startOfDay()->toDateTimeString();
        $endOfYesterday = $now->copy()->subDay()->endOfDay()->toDateTimeString();

        // Count gym check-ins for today
        $todaysCheckIns = GymEntry::whereBetween('entry_time', [$startOfToday, $endOfToday])
            ->count();

        // Count gym check-ins for yesterday
        $yesterdaysCheckIns = GymEntry::whereBetween('entry_time', [$startOfYesterday, $endOfYesterday])
            ->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($yesterdaysCheckIns > 0) {
            $percentageChange = (($todaysCheckIns - $yesterdaysCheckIns) / $yesterdaysCheckIns) * 100;
        }

        // Determine the arrow indicator
        $arrowIndicator = ($percentageChange >= 0) ? '▲' : '▼';

        // Format the percentage change
        $formattedPercentageChange = abs(round($percentageChange, 2)) . '% vs Yesterday ' . $arrowIndicator;

        return [
            'todaysCheckIns' => $todaysCheckIns,
            'formattedPercentageChange' => $formattedPercentageChange,
        ];
    }

    public function getExpiringMemberships()
    {
        // Define the date range for expiring memberships (next 7 days)
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(7)->toDateString();

        // Count members whose memberships are expiring within the next 7 days
        $expiringMemberships = User::whereBetween('end_date', [$startDate, $endDate])
            ->count();

        return $expiringMemberships;
    }
}
