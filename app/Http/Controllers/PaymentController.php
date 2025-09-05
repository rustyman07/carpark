<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(){
        $payments = Payment::with('card')->get();
return Inertia('Payments/Index', [
    'payments' => $payments,
]);

    }
}
