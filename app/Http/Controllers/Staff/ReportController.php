<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Payment;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    /**
     * Display the report page.
     */
    public function index()
    {
        return view('staff.report');
    }

    /**
     * Generate reports based on filters.
     */
    public function generateReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'type' => 'required|in:finance,members',
            'period' => 'required|in:today,thisWeek,thisMonth,thisYear,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $request->input('type'); // 'finance' or 'members'
        $period = $request->input('period'); // 'today', 'thisWeek', 'thisMonth', 'thisYear', 'custom'
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch data based on type and period
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

        // Apply filters based on period
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
        $query = Member::query();

        // Apply filters based on period
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
        // Validate the request
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

        // Fetch data based on type and period
        if ($type === 'finance') {
            $data = $this->generateFinanceReport($period, $startDate, $endDate);
            $pdf = PDF::loadView('staff.reports.finance-pdf', $data);
        } else {
            $data = $this->generateMembersReport($period, $startDate, $endDate);
            $pdf = PDF::loadView('staff.reports.members-pdf', $data);
        }

        // Download the PDF
        return $pdf->download("{$type}-report-{$period}.pdf");
    }
}