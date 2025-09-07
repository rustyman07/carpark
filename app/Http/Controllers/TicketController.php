<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\CardInventoryDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

           return inertia('Parkin/Index',);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Validate inputs
    $data = $request->validate([
        'PLATENO'    => 'required|string',
        'PARKYEAR'   => 'nullable|integer',
        'PARKMONTH'  => 'nullable|integer',
        'PARKDAY'    => 'nullable|integer',
        'PARKHOUR'   => 'nullable|integer',
        'PARKMINUTE' => 'nullable|integer',
        'PARKSECOND' => 'nullable|integer',
    ]);

    // Build PARKDATETIME from inputs (fallback: now)
    $parkDateTime = isset($data['PARKYEAR'], $data['PARKMONTH'], $data['PARKDAY'])
        ? Carbon::create(
            $data['PARKYEAR'],
            $data['PARKMONTH'],
            $data['PARKDAY'],
            $data['PARKHOUR']   ?? 0,
            $data['PARKMINUTE'] ?? 0,
            $data['PARKSECOND'] ?? 0
        )
        : now();

    // Always sync fields
    $data['PARKYEAR']   = $parkDateTime->year;
    $data['PARKMONTH']  = $parkDateTime->month;
    $data['PARKDAY']    = $parkDateTime->day;
    $data['PARKHOUR']   = $parkDateTime->hour;
    $data['PARKMINUTE'] = $parkDateTime->minute;
    $data['PARKSECOND'] = $parkDateTime->second;
    $data['PARKDATETIME'] = $parkDateTime;

    // Defaults
    $data['CANCELLED'] = 0;
   $data['TICKETNO'] = 0;
    DB::transaction(function () use ($data) {
        $ticket = Ticket::create($data);
        $ticket->update([
            'TICKETNO' => (string) (1000000 + $ticket->id),
            'uuid' => (string) Str::uuid(),]);
    });

    return back()->with('success', 'Ticket created successfully!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return back()->with('success', 'Ticket has been voided');

    }

    public function showLogs(Request $request)
    {

        $type     = $request->input('type', 'PARK-IN'); // default
        $dateFrom = $request->input('dateFrom', now()->toDateString()); // default = today
       $dateTo   = $request->input('dateTo', now()->toDateString());   // default = today


        $query = Ticket::whereNull('deleted_at')
            ->where('ISPARKOUT',$type ==='PARK-IN'? 0 : 1)
            ->select('id','TICKETNO', 'PLATENO', 'PARKDATETIME', 'PARKOUTDATETIME','REMARKS')
            ->orderByDesc('created_at');

        $dateColumn = $type === 'PARK-IN' ? 'PARKDATETIME' : 'PARKOUTDATETIME';


        if ($dateFrom && $dateTo) {
            $query->whereDate($dateColumn, '>=', $dateFrom)
                ->whereDate($dateColumn, '<=', $dateTo);
        }

        return inertia('Logs/Index', [ 
       'Tickets' => $query->paginate(5)->withQueryString(), 

        ]);

    }

        public function park_out()
        {
    
            return inertia('Parkout/Index', [
                'ticket' => session('ticket'),
                'success' => session('success'),
            ]);
        }


public function submit_park_out(Request $request)
{
    // 1️⃣ Validate input
    $data = $request->validate([
        'PLATENO'       => 'required|string|exists:tickets,PLATENO',
        'PARKOUTYEAR'   => 'nullable|integer',
        'PARKOUTMONTH'  => 'nullable|integer',
        'PARKOUTDAY'    => 'nullable|integer',
        'PARKOUTHOUR'   => 'nullable|integer',
        'PARKOUTMINUTE' => 'nullable|integer',
        'PARKOUTSECOND' => 'nullable|integer',
    ], [
        'PLATENO.required' => 'Plate number is required',
        'PLATENO.exists'   => 'Plate number not found',
    ]);

    // 2️⃣ Compute PARKOUTDATETIME (use provided or fallback to now)
  $parkOutDateTime = isset($data['PARKOUTYEAR'], $data['PARKOUTMONTH'], $data['PARKOUTDAY'])
    ? Carbon::create(
        $data['PARKOUTYEAR'],
        $data['PARKOUTMONTH'],
        $data['PARKOUTDAY'],
        $data['PARKOUTHOUR']   ?? 0,
        $data['PARKOUTMINUTE'] ?? 0,
        $data['PARKOUTSECOND'] ?? 0
    )
    : now();

        $data['PARKOUTYEAR']   = $parkOutDateTime->year;
        $data['PARKOUTMONTH']  = $parkOutDateTime->month;
        $data['PARKOUTDAY']    = $parkOutDateTime->day;
        $data['PARKOUTHOUR']   = $parkOutDateTime->hour;
        $data['PARKOUTMINUTE'] = $parkOutDateTime->minute;
        $data['PARKOUTSECOND'] = $parkOutDateTime->second;
        $data['PARKOUTDATETIME'] = $parkOutDateTime;

    //  $ticket = Ticket::where('PLATENO', $data['PLATENO'])
    //      ->where('REMARKS',0)
    //     ->latest('PARKDATETIME')
    //     ->first();

    $ticket = Ticket::whereNull('deleted_at')
    ->where('PLATENO', $data['PLATENO'])
    ->where(function ($q) {
        $q->whereIn('REMARKS', ['UNPAID'])
          ->orWhereNull('REMARKS');
    })
    ->latest('PARKDATETIME')
    ->first();


        if (!$ticket){
            return redirect()->back()->with([ 
                'error' => "Hasn't Park in Yet",
                'success' => false
        
        
        ]);
        }
    
    // 4️⃣ Calculate fee
    $company = Company::find(1); 
    $start = Carbon::parse($ticket->PARKDATETIME)->timezone(config('app.timezone'));
    $end =   Carbon::parse( $data['PARKOUTDATETIME'])->timezone(config('app.timezone'));

    $minutesDiff = $start->diffInMinutes($end);          // absolute minutes
    $daysParked  = max(1, (int) ceil($minutesDiff / (24 * 60))); // 1440 minutes = 1 day

    $ticket->PARKFEE = (int) $daysParked * (float) $company->post_paid_rate;

    $ticket->fill([
        'ISPARKOUT'     => 1,
        'REMARKS'       => 'UNPAID',
        'PARKOUTYEAR'   => $data['PARKOUTYEAR'],
        'PARKOUTMONTH'  => $data['PARKOUTMONTH'],
        'PARKOUTDAY'    => $data['PARKOUTDAY'],
        'PARKOUTHOUR'   => $data['PARKOUTHOUR'],
        'PARKOUTMINUTE' => $data['PARKOUTMINUTE'],
        'PARKOUTSECOND' => $data['PARKOUTSECOND'],
        'PARKOUTDATETIME' => $parkOutDateTime
    ])->save();


    // 5️⃣ Success response
   
    return redirect()->route('show.payment', [
        'uuid' => $ticket->uuid
    ]);
}


public function show_payment(string $uuid){
    $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

    return Inertia('Parkout/Payment',[
        'ticket' => new TicketResource($ticket),
    ]);
}


// public function submit_payment(Request $request){

//         $ticket = Ticket::find($request->input('ticket_id'));
//         $company = Company::find(1); 
//         $minutesDiff = ceil($ticket->PARKDATETIME->diffInSeconds($ticket->PARKOUTDATETIME) / 60);
//         $daysParked  = max(1, (int) ceil($minutesDiff / (24 * 60)));



//      if ($request->input('mode_of_payment') === 'card')
//      {
//           $request->validate([
//             'ticket_id' => 'required|exists:tickets,id',
//         ], [
//             'ticket_id.exists' => 'Invalid Payment.',
//         ]);



//      }else if ($request->input('mode_of_payment') === 'cash'){
//          $data =   $request->validate([
//         'ticket_id' => 'required|exists:tickets,id',
//         'qr_code'   => 'required|string',
//             ]);
//          $qrCode = $data['qr_code'];


//         $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)->first();
//         if (!$detail) {
//         return back()->with('error', 'Invalid QR Code.');
//         }

//         if ($detail->balance <= 0){
//         return back()->with('error', 'Card already used up.');
//        }

//         if ((int)$detail->balance < $daysParked) {
//         return back()->with('error', 'Insufficient Balance. '.$ticket->balance);
//       }
    
//     }
//        ;


//     DB::transaction(function () use ($detail, $ticket, $daysParked) {
//         $detail->balance -= $daysParked;
//         $detail->status   = $detail->balance > 0 ? 'ACTIVE' : 'USED';
//         $detail->save();

//         $ticket->REMARKS  = 'PAID';
//         $ticket->ISPARKOUT = 1;
//         $ticket->save();
//     });


//    return redirect()->route('parkout.receipt')->with([
//         'success' => true,
//         'message' => 'Payment successful! Remaining balance: '.$detail->balance.' | Days parked: '.$daysParked,
//         'ticket'  => $ticket,
//         'detail'  => $detail,
//         'company' => $company
//    ]);
    
// }
    

public function submit_payment(Request $request)
{
    $validationRules = [
        'ticket_id' => 'required|exists:tickets,id',
        'mode_of_payment' => 'required|in:cash,card',
    ];

    if ($request->input('mode_of_payment') === 'card') {
        $validationRules['qr_code'] = 'required|string';
    }

    $request->validate($validationRules);

    $ticket = Ticket::findOrFail($request->ticket_id);
    $company = Company::find(1);

    if ($ticket->REMARKS === 'PAID') {
        return back()->with('error', 'This ticket has already been paid.');
    }

    // Calculate parking days
    $minutesDiff = ceil($ticket->PARKDATETIME->diffInSeconds($ticket->PARKOUTDATETIME) / 60);
    $daysParked = max(1, (int) ceil($minutesDiff / (24 * 60)));

    $transactionDetails = [];
    $amount = (float) $daysParked * (float) $company->post_paid_rate; // standard rate
    $cardId = null;
    $qrCode = null;

    if ($request->mode_of_payment === 'cash') {
        // normal cash payment
    } else if ($request->mode_of_payment === 'card') {
        $qrCode = $request->qr_code;

        $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)->first();
        if (!$detail) {
            return back()->with('error', 'Invalid QR Code.');
        }

        if ($detail->balance < $daysParked) {
            return back()->with('error', 'Insufficient Balance.');
        }

        // link ticket to QR
        $ticket->QRCODE = $qrCode;

        $transactionDetails['detail'] = $detail;
        $transactionDetails['days_parked'] = $daysParked;
        $cardId = $detail->id;

        // IMPORTANT: since prepaid, we set "amount" to 0
        $amount = 0;
    }

    DB::transaction(function () use ($ticket, $transactionDetails, $request, $amount, $cardId, $qrCode, $daysParked) {
        // Update ticket
        $ticket->REMARKS = 'PAID';
        $ticket->mode_of_payment = $request->mode_of_payment;
        $ticket->PARKFEE = $amount; // for prepaid card users, this will be 0
        $ticket->save();

        // Create payment record (for audit/history)
        Payment::create([
            'ticket_id'      => $ticket->id,
            'card_id'        => $cardId,
            'qr_code'        => $qrCode,
            'amount'         => $amount, // 0 if prepaid card was used
            'days_deducted'  => $request->mode_of_payment === 'card' ? $daysParked : null,
            'payment_type'   => 'ticket',
            'payment_method' => $request->mode_of_payment,
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        // Deduct balance if card was used
        if (isset($transactionDetails['detail'])) {
            $detail = $transactionDetails['detail'];
            $daysParked = $transactionDetails['days_parked'];
            $detail->balance -= $daysParked;
            $detail->status = $detail->balance > 0 ? 'ACTIVE' : 'USED';
            $detail->save();
        }
    });

    $responseWith = [
        'success' => 'Payment successful!',
        'id'      => $ticket->uuid,
        'company' => $company,
    ];

    if (isset($transactionDetails['detail'])) {
        $responseWith['detail'] = $transactionDetails['detail'];
    }

    return redirect()
        ->route('parkout.receipt', ['id' => $ticket->uuid])
        ->with($responseWith);
}


    public function parkout_receipt(Request $request){

        $ticket = Ticket::where('uuid',$request->id)->first();
        if(!$ticket){
                //  return back()->with('error', 'Insufficient Balance.');
                 abort(404, 'Ticket not found');
        }
         $balance = null;

         $payment = Payment::where('ticket_id',$ticket->id)->first();
    
        if ($ticket->mode_of_payment ==='card'){
           $detail = CardInventoryDetail::where('qr_code_hash',$ticket->QRCODE)->first();
           if(!$detail){
            //  return back()->with('error', 'Invalid QR Code.');
              abort(404, 'Invalid QR Code');
           }
           $balance = $detail?->balance;
        }
   

        $company = Company::findOrFail(1);

        return Inertia('Parkout/Receipt',[
            'ticket' => (new TicketResource($ticket))->resolve(),
            'balance' => $balance,
            'company' => $company,
            'amount'  => $payment?->amount ?? 0.00,
            
        ]);
    }



}