<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use PDF;
use App\Models\Attendance;

class ReportController extends Controller
{
    /**
     * Display the report page.
     */
    public function index(Request $request)
    {
        // Get the selected filter type from the URL (default: all time)
        $filterType = $request->query('filter', 'all');
        $dateFilter = $request->query('date', now()->toDateString());
        
        // Determine the date based on the filter type
        switch ($filterType) {
            case 'yesterday':
                $dateFilter = now()->subDay()->toDateString();
                break;
            case 'today':
                $dateFilter = now()->toDateString();
                break;
            case 'custom':
                $dateFilter = $request->query('date', now()->toDateString()); // Default to custom date if available
                break;
            default:
                $dateFilter = now()->toDateString(); // Default to today if no filter
        }
        
        // Fetch users who have attendance records for the selected date
        $members = User::select('id', 'first_name', 'last_name', 'membership_type', 'member_status', 'phone_number', 'start_date', 'end_date', 'rfid_uid')
            ->where('role', 'user')
            ->whereHas('attendance', function ($query) use ($dateFilter) {
                $query->whereDate('time_in', $dateFilter);
            })
            ->get(); 

        // Fetch attendance records for the selected members
        foreach ($members as $member) {
            $member->attendance = Attendance::where('rfid_uid', $member->rfid_uid)
                ->whereDate('time_in', $dateFilter)
                ->first();
        }

    
        // Fetch payments with user data
        $payments = Payment::with('user')
            ->select('rfid_uid', 'payment_date', 'amount', 'payment_method')
            ->get();
    
        return view('staff.report', compact('members', 'payments', 'filterType', 'dateFilter'));
    }
    
    

    /**
     * Generate reports based on filters.
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:finance,members',
            'period' => 'required|in:today,thisWeek,thisMonth,thisYear,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $request->input('type');
        $period = $request->input('period');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($type === 'finance') {
            $data = $this->generateFinanceReport($period, $startDate, $endDate);
        } else {
            $data = $this->generateMembersReport($period, $startDate, $endDate);
        }

        return response()->json($data);
    }

    /**
     * Generate finance report.
     */
    private function generateFinanceReport($period, $startDate = null, $endDate = null)
    {
        $query = Payment::query();
    
        switch ($period) {
            case 'today':
                $query->whereDate('payment_date', Carbon::today());
                break;
            case 'thisWeek':
                $query->whereBetween('payment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'thisMonth':
                $query->whereMonth('payment_date', Carbon::now()->month);
                break;
            case 'thisYear':
                $query->whereYear('payment_date', Carbon::now()->year);
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->whereBetween('payment_date', [$startDate, $endDate]);
                }
                break;
        }
    
        $payments = $query->get();
        $totalRevenue = $payments->sum('amount');
    
        return [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue,
        ];
    }
    
    /**
     * Generate members report.
     */
    private function generateMembersReport($period, $startDate = null, $endDate = null)
    {
        $query = User::where('role', 'user');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'thisWeek':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'thisMonth':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'thisYear':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                break;
        }

        $members = $query->get();
        $totalMembers = $members->count();

        return [
            'members' => $members,
            'totalMembers' => $totalMembers,
        ];
    }

    /**
     * Export report as PDF.
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:finance,members',
            'period' => 'required|in:today,thisWeek,thisMonth,thisYear,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $request->input('type');
        $period = $request->input('period');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($type === 'finance') {
            $data = $this->generateFinanceReport($period, $startDate, $endDate);
            $pdf = PDF::loadView('staff.reports.finance-pdf', ['data' => $data]);
        } else {
            $data = $this->generateMembersReport($period, $startDate, $endDate);
            $pdf = PDF::loadView('staff.reports.members-pdf', ['data' => $data]);
        }

        return $pdf->download("{$type}-report-{$period}.pdf");
    }
}
