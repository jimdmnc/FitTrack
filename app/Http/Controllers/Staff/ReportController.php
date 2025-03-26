<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use PDF;
use App\Models\Attendance;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReportController extends Controller
{
    /**
     * Display the report page.
     */
    public function index(Request $request)
    {
        // Get the selected filter value
        $filter = $request->input('filter', '');
        $perPage = $request->input('per_page', 10);
    
        // Base queries with eager loading
        $attendancesQuery = Attendance::with('user');
        $paymentsQuery = Payment::with('user');
    
        // Get dates in application timezone
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Apply filters
        if ($filter == 'today') {
            $attendancesQuery->whereDate('time_in', $today);
            $paymentsQuery->whereDate('payment_date', $today);
        } elseif ($filter == 'yesterday') {
            $yesterday = Carbon::yesterday();
            $attendancesQuery->whereDate('time_in', $yesterday);
            $paymentsQuery->whereDate('payment_date', $yesterday);
        } elseif ($filter == 'last7') {
            $attendancesQuery->whereBetween('time_in', [Carbon::now()->subDays(7), $now]);
            $paymentsQuery->whereBetween('payment_date', [Carbon::now()->subDays(7), $now]);
        } elseif ($filter == 'last30') {
            $attendancesQuery->whereBetween('time_in', [Carbon::now()->subDays(30), $now]);
            $paymentsQuery->whereBetween('payment_date', [Carbon::now()->subDays(30), $now]);
        } elseif ($filter == 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
    
            if ($startDate && $endDate) {
                $attendancesQuery->whereBetween('time_in', [$startDate, $endDate]);
                $paymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
            }
        }
    
        // Get results (with pagination if needed)
        $attendances = $attendancesQuery->get();
        $payments = $paymentsQuery->get();
    
        return view('staff.report', compact('attendances', 'payments'));
    }
    
    
    
    public function generateReport(Request $request)
    {
        // Get the type of report requested ('members' or 'payments')
        $type = $request->get('type');
        $filter = $request->get('date_filter', '');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
    
        // Base queries
        $attendancesQuery = Attendance::query();
        $paymentsQuery = Payment::query();
    
        // Get dates in application timezone
        $today = Carbon::today();
        $now = Carbon::now();
    
        // Apply filters
        if ($filter == 'today') {
            $attendancesQuery->whereDate('time_in', $today);
            $paymentsQuery->whereDate('payment_date', $today);
        } elseif ($filter == 'yesterday') {
            $yesterday = Carbon::yesterday();
            $attendancesQuery->whereDate('time_in', $yesterday);
            $paymentsQuery->whereDate('payment_date', $yesterday);
        } elseif ($filter == 'last7') {
            $attendancesQuery->whereBetween('time_in', [Carbon::now()->subDays(7), $now]);
            $paymentsQuery->whereBetween('payment_date', [Carbon::now()->subDays(7), $now]);
        } elseif ($filter == 'last30') {
            $attendancesQuery->whereBetween('time_in', [Carbon::now()->subDays(30), $now]);
            $paymentsQuery->whereBetween('payment_date', [Carbon::now()->subDays(30), $now]);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $attendancesQuery->whereBetween('time_in', [$startDate, $endDate]);
            $paymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
    
        // Fetch filtered data
        $attendances = $attendancesQuery->get();
        $payments = $paymentsQuery->get();
    
        // Initialize Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
    
        // Generate the appropriate report based on the 'type' parameter
        if ($type == 'members') {
            $html = view('reports.members_report', compact('attendances'))->render();
        } elseif ($type == 'payments') {
            $html = view('reports.payments_report', compact('payments'))->render();
        } else {
            return response()->json(['error' => 'Invalid report type'], 400);
        }
    
        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);
    
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
    
        // Render PDF (first pass, to parse the HTML)
        $dompdf->render();
    
        // Stream the PDF or download it
        return $dompdf->stream($type . '_report.pdf');
    }
    

    /**
     * Generate reports based on filters.
     */
    // public function generateReport(Request $request)
    // {
    //     $request->validate([
    //         'type' => 'required|in:finance,members',
    //         'period' => 'required|in:today,thisWeek,thisMonth,thisYear,custom',
    //         'start_date' => 'nullable|date',
    //         'end_date' => 'nullable|date|after_or_equal:start_date',
    //     ]);

    //     $type = $request->input('type');
    //     $period = $request->input('period');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     if ($type === 'finance') {
    //         $data = $this->generateFinanceReport($period, $startDate, $endDate);
    //     } else {
    //         $data = $this->generateMembersReport($period, $startDate, $endDate);
    //     }

    //     return response()->json($data);
    // }

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
