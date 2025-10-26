<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{



public function index(Request $request)
{

    $dateFrom = $request->input('dateFrom', now()->toDateString());
    $dateTo   = $request->input('dateTo', now()->toDateString());
    $type     = $request->input('type', 'All');

    $from = Carbon::parse($dateFrom)->startOfDay();
    $to   = Carbon::parse($dateTo)->endOfDay();

    // ✅ Query includes all times in that range
    $query = Payment::with(['ticket', 'details', 'user'])
        ->whereBetween('paid_at', [$from, $to]);

    if ($type !== 'All') {
        $query->where('payment_type', $type);
    }

    $payments = $query->orderBy('paid_at', 'desc')->get();

    return Inertia::render('Payments/Index', [
        'payments' => $payments,
        'filters' => [
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'type'     => $type,
        ],
    ]);
}





    public function generatePaymentReport(Request $request)
{
    // $startDate = $request->input('start_date', now()->startOfDay());
    // $endDate = $request->input('end_date', now()->endOfDay());*

        $startDate = $request->input('dateFrom') 
        ? Carbon::parse($request->input('dateFrom'))->startOfDay()
        : now()->startOfDay();

    $endDate = $request->input('dateTo') 
        ? Carbon::parse($request->input('dateTo'))->endOfDay()
        : now()->endOfDay();

    $type = $request->input('type'); 
    

    $payments = Payment::with(['ticket', 'details'])
        ->whereBetween('paid_at', [$startDate, $endDate])
        ->where('status', 'paid') 
        ->orderBy('paid_at', 'asc')
        ->get();
    
 
    $totalPayments = $payments->count();
    $totalAmount = $payments->sum('total_amount');
    $totalChange = $payments->sum('change');
    $totalCollected = $payments->sum('total_amount');
    
    // Group by payment method
    $cashTotal = $payments->where('payment_method', 'Cash')->sum('amount');
    $gcashTotal = $payments->where('payment_method', 'Gcash')->sum('total_amount');
    $cardTotal = $payments->where('payment_method', 'Card')->sum('total_amount');
    
    // Group by payment type
    $regularPayments = $payments->where('payment_type', 'regular')->sum('amount');
    $cardPayments = $payments->where('payment_type', 'card')->sum('amount');
    
    // Company information
    $company =  Company::first();
    
    // Load logo as base64
    $logoPath = public_path('images/comlogo.png');
    $logoBase64 = file_exists($logoPath) 
        ? base64_encode(file_get_contents($logoPath)) 
        : '';
    
    $reportDate = now()->format('F d, Y');
    $dateRange = \Carbon\Carbon::parse($startDate)->format('M d, Y') . ' - ' . 
                 \Carbon\Carbon::parse($endDate)->format('M d, Y');
    $preparedBy = Auth::user()->name ?? 'N/A';
    

    $pdf = PDF::loadView('Printables.RevenueReport', compact(
        'payments',
        'totalPayments',
        'totalAmount',
        'totalChange',
        'totalCollected',
        'cashTotal',
        'gcashTotal',
        'cardTotal',
        'regularPayments',
        'cardPayments',
        'company',
        'logoBase64',
        'reportDate',
        'dateRange',
        'preparedBy'
    ));
    
    return $pdf->stream('payment-report-' . now()->format('Y-m-d') . '.pdf');
}




}
