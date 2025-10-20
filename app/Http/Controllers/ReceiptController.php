<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Company;
use App\Http\Resources\TicketResource;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{

   public function index(Request $request){

        $ticket = Ticket::where('uuid',$request->id)->first();
        if(!$ticket){
                //  return back()->with('error', 'Insufficient Balance.');
                 abort(404, 'Ticket not found');
        }
      
        $payment = Payment::where('ticket_id',$ticket->id)->first();

        $details =  $payment->details;
        $balance = $payment->balance;
        $company = Company::findOrFail(1);

        return Inertia('Parkout/Receipt',[
            'ticket' => (new TicketResource($ticket))->resolve(),
            'payment' => $payment,
            'company' => $company,
            'details' => $details
            
        ]);
    }


public function printReceipt($uuid)
{
    $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
    $payment = Payment::where('ticket_id', $ticket->id)->firstOrFail();

    // Decode details — single card info
    $details = $payment->details;

    $company = Company::findOrFail(1);
    $logoPath = public_path('images/comlogo.png');

    // Generate barcode
    $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = base64_encode($barcodeGenerator->getBarcode($ticket->ticket_no, $barcodeGenerator::TYPE_CODE_128));

    $data = [
        'ticket' => $ticket,
        'payment' => $payment,
        'details' => $details,
        'company' => $company,
        'barcode' => $barcode,
        'logoPath' => $logoPath,
    ];

    $pdf = Pdf::loadView('Printables.Receipt', $data)
        ->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm x 297mm

    return $pdf->stream('receipt-' . $ticket->ticket_no . '.pdf');
}


    
}
