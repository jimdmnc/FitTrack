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
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Display the report page.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', '');
        $type = $request->input('type', 'members');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 10);

        // Use application timezone consistently
        $timezone = config('app.timezone');
        $today = Carbon::today($timezone);
        $now = Carbon::now($timezone);
        
        // Process members report
        $attendancesQuery = Attendance::with('user')->orderBy('time_in', 'desc');
        $paymentsQuery = Payment::with('user')->orderBy('payment_date', 'desc');
        
        if ($filter == 'today') {
            $attendancesQuery->whereDate('time_in', $today);
            $paymentsQuery->whereDate('payment_date', $today);
        } elseif ($filter == 'yesterday') {
            $yesterday = Carbon::yesterday($timezone);
            $attendancesQuery->whereDate('time_in', $yesterday);
            $paymentsQuery->whereDate('payment_date', $yesterday);
        } elseif ($filter == 'last7') {
            $attendancesQuery->where('time_in', '>=', $now->copy()->subDays(7)->startOfDay());
            $paymentsQuery->where('payment_date', '>=', $now->copy()->subDays(7)->startOfDay());
        } elseif ($filter == 'last30') {
            $attendancesQuery->where('time_in', '>=', $now->copy()->subDays(30)->startOfDay());
            $paymentsQuery->where('payment_date', '>=', $now->copy()->subDays(30)->startOfDay());
        } elseif ($filter == 'custom') {
            if ($startDate && $endDate) {
                // Convert to Carbon with timezone and include full end date
                $start = Carbon::parse($startDate, $timezone)->startOfDay();
                $end = Carbon::parse($endDate, $timezone)->endOfDay();
                
                $attendancesQuery->whereBetween('time_in', [$start, $end]);
                $paymentsQuery->whereBetween('payment_date', [$start, $end]);
            }
        }

        // Paginate results while preserving all query parameters
        $queryParams = $request->except('page'); // preserve all query parameters except page
        
        $attendances = $attendancesQuery->paginate($perPage)
            ->appends($queryParams);
            
        $payments = $paymentsQuery->paginate($perPage)
            ->appends($queryParams);

        return view('staff.report', compact(
            'attendances', 
            'payments', 
            'filter',
            'type',
            'startDate',
            'endDate',
            'perPage'
        ));
    }
    

    public function generateReport(Request $request)
{
    // Get filter parameters from the request
    $type = $request->get('type');
    $filter = $request->get('filter', '');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    
    // Validate the report type
    if (!in_array($type, ['members', 'payments'])) {
        // Redirect back with error message instead of returning JSON
        return redirect()->back()->with('error', 'Invalid report type selected.');
    }

    // Set the query based on the report type
    $query = $type === 'members' 
        ? Attendance::with('user')->orderBy('time_in', 'desc')
        : Payment::with('user')->orderBy('payment_date', 'desc');

    $timezone = config('app.timezone');
    $today = Carbon::today($timezone);
    $now = Carbon::now($timezone);

    // Apply date filters
    if ($filter == 'today') {
        $query->whereDate($type === 'members' ? 'time_in' : 'payment_date', $today);
    } elseif ($filter == 'yesterday') {
        $query->whereDate($type === 'members' ? 'time_in' : 'payment_date', Carbon::yesterday($timezone));
    } elseif ($filter == 'last7') {
        $query->where($type === 'members' ? 'time_in' : 'payment_date', '>=', $now->copy()->subDays(7)->startOfDay());
    } elseif ($filter == 'last30') {
        $query->where($type === 'members' ? 'time_in' : 'payment_date', '>=', $now->copy()->subDays(30)->startOfDay());
    } elseif ($filter == 'custom' && $startDate && $endDate) {
        // Convert to Carbon with timezone
        $start = Carbon::parse($startDate, $timezone)->startOfDay();
        $end = Carbon::parse($endDate, $timezone)->endOfDay();
        
        // Ensure dates are within valid range
        $today = Carbon::today($timezone);
        $start = min($start, $today);
        $end = min($end, $today);
        
        // Ensure end date is after start date
        if ($end < $start) {
            $end = $start;
        }
        
        $query->whereBetween($type === 'members' ? 'time_in' : 'payment_date', [$start, $end]);
    }

    // Get the data
    $data = $query->get();

    if ($data->isEmpty()) {
        // Redirect back with error message instead of returning JSON
        return redirect()->back()->with('warning', 'No data found for the selected filters. Please adjust your filter criteria and try again.');
    }

    // Prepare view data - ensure we use the correct variable names
    $viewData = [
        'attendances' => $type === 'members' ? $data : collect(),
        'payments' => $type === 'payments' ? $data : collect(),
        'type' => $type,
        'filter' => $filter,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'timezone' => $timezone,
    ];

    try {
        // Select the correct view
        $view = $type === 'members' ? 'reports.members_report' : 'reports.payments_report';

        // Setup DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view($view, $viewData)->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "{$type}_report_" . now()->format('Y_m_d_H_i_s') . ".pdf";
        return $dompdf->stream($filename);
    } catch (\Exception $e) {
        // Handle PDF generation errors gracefully
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to generate the report. Please try again later.');
    }
}
    
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
                    // Validate dates are not in the future
                    $today = Carbon::today()->format('Y-m-d');
                    validator([
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ], [
                        'start_date' => 'required|date|before_or_equal:'.$today,
                        'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:'.$today
                    ])->validate();
                    
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
    
    private function generateMembersReport($period, $startDate = null, $endDate = null)
    {
        $query = User::where(function($query) {
            $query->where('role', 'user')
                  ->orWhere('role', 'userSession');
        });
    
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
                    // Validate dates are not in the future
                    $today = Carbon::today()->format('Y-m-d');
                    validator([
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ], [
                        'start_date' => 'required|date|before_or_equal:'.$today,
                        'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:'.$today
                    ])->validate();
                    
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

        // Validate dates are not in the future for custom range
        if ($period === 'custom') {
            $today = Carbon::today()->format('Y-m-d');
            $request->validate([
                'start_date' => 'required|date|before_or_equal:'.$today,
                'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:'.$today
            ]);
        }

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