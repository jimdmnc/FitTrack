<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Make sure to import the User model
use Carbon\Carbon;
use App\Models\Attendance;


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
        $newMembersData = $this->getNewMembersData();
        $todaysCheckInsData = $this->getTodaysCheckInsData();
        $expiringMemberships = $this->getExpiringMemberships();
        $peakHours = $this->getPeakHours();
        $membershipData = $this->getMembershipTypeData(); // Fetch membership type data
        $dailyCheckIns = $this->getDailyCheckIns();
        $weeklyCheckIns = $this->getWeeklyCheckIns();
        $monthlyCheckIns = $this->getMonthlyCheckIns();
        $yearlyCheckIns = $this->getYearlyCheckIns();
        $topActiveMembers = $this->getTopActiveMembers(); // Get top 10 active members

        return view('staff.dashboard', compact(
            'members', 
            'query', 
            'newMembersData', 
            'todaysCheckInsData', 
            'expiringMemberships', 
            'peakHours', 
            'membershipData',
            'dailyCheckIns',  // Last 7 days
            'weeklyCheckIns', // Last 4 weeks
            'monthlyCheckIns', // Last 12 months
            'yearlyCheckIns',  // Last 5 years
            'topActiveMembers' // Pass to view
        ));
    }

    


    public function getTopActiveMembers()
    {
        return User::where('role', 'user')
            ->where('member_status', 'active')
            ->leftJoin('attendances', 'users.rfid_uid', '=', 'attendances.rfid_uid')
            ->select(
                'users.id', 
                'users.rfid_uid', // Include RFID UID
                'users.first_name', 
                'users.last_name', 
                'users.membership_type', // Include membership type
                'users.member_status', // Include member status
                \DB::raw('COUNT(attendances.rfid_uid) as check_ins_count')
            )
            ->groupBy('users.id', 'users.rfid_uid', 'users.first_name', 'users.last_name', 'users.membership_type', 'users.member_status') // Add to GROUP BY
            ->orderByDesc('check_ins_count')
            ->limit(10)
            ->get();
    }
    
    










    // private function getActiveMembersData()
    // {
    //     // Get the current date and the start of the current week
    //     $now = Carbon::now();
    //     $startOfCurrentWeek = $now->startOfWeek()->toDateTimeString();
    //     $endOfCurrentWeek = $now->endOfWeek()->toDateTimeString();

    //     // Get the start of last week
    //     $startOfLastWeek = $now->copy()->subWeek()->startOfWeek()->toDateTimeString();
    //     $endOfLastWeek = $now->copy()->subWeek()->endOfWeek()->toDateTimeString();

    //     // Count active members this week
    //     $currentWeekActiveMembers = User::where('role', 'user')
    //         ->where('member_status', 'active')
    //         ->whereBetween('start_date', [$startOfCurrentWeek, $endOfCurrentWeek])
    //         ->count();

    //     // Count active members last week
    //     $lastWeekActiveMembers = User::where('role', 'user')
    //         ->where('member_status', 'active')
    //         ->whereBetween('start_date', [$startOfLastWeek, $endOfLastWeek])
    //         ->count();

    //     // Calculate percentage change
    //     $percentageChange = 0;
    //     if ($lastWeekActiveMembers > 0) {
    //         $percentageChange = (($currentWeekActiveMembers - $lastWeekActiveMembers) / $lastWeekActiveMembers) * 100;
    //     }

    //     // Determine the arrow indicator
    //     $arrowIndicator = ($percentageChange >= 0) ? '▲' : '▼';

    //     // Format the percentage change
    //     $formattedPercentageChange = abs(round($percentageChange, 2)) . '% vs Last Week ' . $arrowIndicator;

    //     return [
    //         'currentWeekActiveMembers' => $currentWeekActiveMembers,
    //         'formattedPercentageChange' => $formattedPercentageChange,
    //     ];
    // }

    private function getNewMembersData()
    {
        // Get the current date and the start and end of the current week
        $now = Carbon::now();
        $startOfCurrentWeek = $now->startOfWeek()->toDateTimeString();
        $endOfCurrentWeek = $now->endOfWeek()->toDateTimeString();
    
        // Get the start and end of last week
        $startOfLastWeek = $now->copy()->subWeek()->startOfWeek()->toDateTimeString();
        $endOfLastWeek = $now->copy()->subWeek()->endOfWeek()->toDateTimeString();
    
        // Count new members registered **this week only**
        $currentWeekNewMembers = User::where('role', 'user')
            ->whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->count();
    
        // Count new members registered **last week only**
        $lastWeekNewMembers = User::where('role', 'user')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();
    
        // Calculate percentage change (Max 100%)
        $percentageChange = 0;
        if ($lastWeekNewMembers > 0) {
            $percentageChange = (($currentWeekNewMembers - $lastWeekNewMembers) / $lastWeekNewMembers) * 100;
            $percentageChange = min($percentageChange, 100); // ✅ Limit to max 100%
        }
    
        // Determine the arrow indicator
        $isIncrease = $percentageChange >= 0;
        $arrowIndicator = $isIncrease ? '▲' : '▼';
    
        // Format the percentage change
        $formattedPercentageChange = abs(round($percentageChange, 2)) . '% vs Last Week ' . $arrowIndicator;
    
        return [
            'currentWeekNewMembers' => $currentWeekNewMembers,  // ✅ Only new members **this week**
            'formattedPercentageChange' => $formattedPercentageChange,
        ];
    }
    

    private function getTodaysCheckInsData()
    {
        // Get the current date range
        $now = Carbon::now();
        $startOfToday = $now->startOfDay()->toDateTimeString();
        $endOfToday = $now->endOfDay()->toDateTimeString();
    
        // Get yesterday’s date range
        $startOfYesterday = $now->copy()->subDay()->startOfDay()->toDateTimeString();
        $endOfYesterday = $now->copy()->subDay()->endOfDay()->toDateTimeString();
    
        // Count today's check-ins from attendances (time_in)
        $todaysCheckIns = Attendance::whereBetween('time_in', [$startOfToday, $endOfToday])->count();
    
        // Count yesterday's check-ins from attendances (time_in)
        $yesterdaysCheckIns = Attendance::whereBetween('time_in', [$startOfYesterday, $endOfYesterday])->count();
    
        // Calculate percentage change
        if ($yesterdaysCheckIns == 0) {
            $percentageChange = $todaysCheckIns > 0 ? 100 : 0; // If no check-ins yesterday, max is 100%
        } else {
            $percentageChange = (($todaysCheckIns - $yesterdaysCheckIns) / $yesterdaysCheckIns) * 100;
            $percentageChange = min($percentageChange, 100); // Limit max increase to 100%
        }
    
        // Determine the arrow indicator
        $arrowIndicator = ($percentageChange >= 0) ? '▲' : '▼';
    
        // Format the percentage change
        $formattedPercentageChange = abs(round($percentageChange, 2)) . '% ' . ($percentageChange > 0 ? 'vs Yesterday' : 'vs Yesterday') . ' ' . $arrowIndicator;
    
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

    // Method to get peak hours data
    public function getPeakHours()
    {
        // Get the attendance data grouped by the hour
        $attendances = Attendance::selectRaw('HOUR(time_in) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    
        // Debugging line to see if the data is being fetched
        // dd($attendances->toArray()); // This will show the raw data as an array
    
        // Prepare labels (hours) and data (frequency of check-ins per hour)
        $labels = [];
        $data = [];
    
        // Iterate over the attendance data to create labels and data for the chart
        foreach ($attendances as $attendance) {
            // Format the hour as '12 AM', '1 PM', etc.
            $labels[] = Carbon::createFromTime($attendance->hour, 0)->format('h A');
            $data[] = $attendance->count;
        }
    
        // Ensure all hours are included, even those with zero attendance
        for ($i = 0; $i < 24; $i++) {
            if (!in_array($i, array_column($attendances->toArray(), 'hour'))) {
                $labels[] = Carbon::createFromTime($i, 0)->format('h A');
                $data[] = 0;  // No check-ins for this hour
            }
        }
    
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    // Add this function inside your DashboardController class

    private function getMembershipTypeData()
    {
        // Fetch the membership data for active users only, grouped by membership type
        $membershipData = User::where('role', 'user')
            ->where('member_status', 'active')  // Only include active users
            ->selectRaw('membership_type, COUNT(*) as count')
            ->groupBy('membership_type')
            ->get();
    
        // Prepare the data for the chart
        $labels = [];
        $data = [];
    
        // Loop through each data point and map the numeric membership type to its label
        foreach ($membershipData as $dataPoint) {
            // Get the label for the membership type using the predefined MEMBERSHIP_TYPES constant
            $membershipLabel = User::MEMBERSHIP_TYPES[$dataPoint->membership_type] ?? 'Unknown';
    
            // Add the label and count to the arrays
            $labels[] = $membershipLabel;
            $data[] = $dataPoint->count;
        }
    
        // Return the data in a format that can be used by the chart
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }



    

   // Get daily check-ins (last 7 days)
   private function getDailyCheckIns()
   {
       return Attendance::selectRaw('DATE(time_in) as date, COUNT(*) as count')
           ->where('time_in', '>=', Carbon::now()->subDays(7))
           ->groupBy('date')
           ->orderBy('date', 'asc')
           ->get();
   }

   // Get weekly check-ins (last 4 weeks)
   private function getWeeklyCheckIns()
   {
       return Attendance::selectRaw('YEARWEEK(time_in, 1) as week, COUNT(*) as count')
           ->where('time_in', '>=', Carbon::now()->subWeeks(4))
           ->groupBy('week')
           ->orderBy('week', 'asc')
           ->get()
           ->map(function ($item) {
               $year = substr($item->week, 0, 4);
               $week = substr($item->week, 4, 2);
               return (object)[
                   'date' => Carbon::now()->setISODate($year, $week)->format('Y-m-d'),
                   'count' => $item->count
               ];
           });
   }

   // Get monthly check-ins (last 12 months)
   private function getMonthlyCheckIns()
   {
       return Attendance::selectRaw('DATE_FORMAT(time_in, "%Y-%m") as month, COUNT(*) as count')
           ->where('time_in', '>=', Carbon::now()->subMonths(12))
           ->groupBy('month')
           ->orderBy('month', 'asc')
           ->get()
           ->map(function ($item) {
               return (object)[
                   'date' => Carbon::createFromFormat('Y-m', $item->month)->format('F Y'),
                   'count' => $item->count
               ];
           });
   }

   // Get yearly check-ins (last 5 years)
   private function getYearlyCheckIns()
   {
       return Attendance::selectRaw('YEAR(time_in) as year, COUNT(*) as count')
           ->where('time_in', '>=', Carbon::now()->subYears(5))
           ->groupBy('year')
           ->orderBy('year', 'asc')
           ->get()
           ->map(function ($item) {
               return (object)[
                   'date' => $item->year,
                   'count' => $item->count
               ];
           });
   }
    
    

    
    
}
