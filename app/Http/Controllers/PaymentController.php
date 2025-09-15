<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(){
          $payments = Payment::orderBy('created_at', 'desc')
                       ->paginate(20); // 20 per page
return Inertia('Payments/Index', [
    'payments' => $payments,
]);

    }
}
