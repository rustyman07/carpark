<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(){
         // $payments = Payment::orderBy('created_at', 'desc')->paginate(20); // 20 per page
     $payments = Payment::orderBy('created_at', 'desc')->with(['ticket', 'details','user'])->get();

return Inertia('Payments/Index', [
    'payments' => $payments,
]);

    }



    public function generatePaymentReport(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfDay());
    $endDate = $request->input('end_date', now()->endOfDay());
    
    // Fetch payments with related data
    $payments = Payment::with(['ticket', 'details'])
        ->whereBetween('paid_at', [$startDate, $endDate])
        ->where('status', 'paid') // or whatever status indicates paid
        ->orderBy('paid_at', 'asc')
        ->get();
    
    // Calculate totals
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
