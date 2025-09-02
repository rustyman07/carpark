<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\CardInventoryDetail;
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
        //
    }

    public function showLogs(Request $request)
    {

        $type     = $request->input('type', 'PARK-IN'); // default
        $dateFrom = $request->input('dateFrom');
        $dateTo   = $request->input('dateTo');

        $query = Ticket::where('CANCELLED', 0)
            ->where('ISPARKOUT',$type ==='PARK-IN'? 0 : 1)
            ->select('TICKETNO', 'PLATENO', 'PARKDATETIME', 'PARKOUTDATETIME')
            ->orderByDesc('created_at');

        $dateColumn = $type === 'PARK-IN' ? 'PARKDATETIME' : 'PARKOUTDATETIME';


        if ($dateFrom && $dateTo) {
            $query->whereDate($dateColumn, '>=', $dateFrom)
                ->whereDate($dateColumn, '<=', $dateTo);
        }

        return inertia('Logs/Index', [
            'Tickets' => $query->paginate(5),

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

    $ticket = Ticket::where('PLATENO', $data['PLATENO'])
        // ->where('ISPARKOUT',0)
        ->latest('PARKDATETIME')
        ->first();

        if (!$ticket){
            return redirect()->back()->with([ 
                'error' => 'Hasnt Park in Yet',
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

    // Update ticket
    // $ticket->fill([
    //     'PARKOUTYEAR'   => $data['PARKOUTYEAR'],
    //     'PARKOUTMONTH'  => $data['PARKOUTMONTH'],
    //     'PARKOUTDAY'    => $data['PARKOUTDAY'],
    //     'PARKOUTHOUR'   => $data['PARKOUTHOUR'],
    //     'PARKOUTMINUTE' => $data['PARKOUTMINUTE'],
    //     'PARKOUTSECOND' => $data['PARKOUTSECOND'],
    //     'PARKOUTDATETIME' => $end
    // ])->save();


//   $ticket->PARKOUTDATETIME = $data['PARKOUTDATETIME'];

  $ticket->REMARKS = 'UNPAID';
  $ticket->PARKOUTDATETIME = $parkOutDateTime;
  $ticket->save();

    // 5️⃣ Success response
   
    return redirect()->route('show.payment', ['uuid' => $ticket->uuid]);
}


public function show_payment(string $uuid){
    $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

    return Inertia('Parkout/Payment',[
        'ticket' => $ticket
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
    // 1. Unified Validation
    // Validate all required fields upfront based on the payment mode.
    $validationRules = [
        'ticket_id' => 'required|exists:tickets,id',
        'mode_of_payment' => 'required|in:cash,card', // Ensure this is one of the valid modes
    ];

    if ($request->input('mode_of_payment') === 'card') {
        $validationRules['qr_code'] = 'required|string';
    }
    
    $request->validate($validationRules);

    // 2. Initial Data Fetching
    // Use findOrFail to automatically handle cases where the ticket is not found,
    // which is more expressive than a manual check.
    $ticket = Ticket::findOrFail($request->input('ticket_id'));
    $company = Company::find(1);
    
    // 3. Calculate Fee Once
    $minutesDiff = ceil($ticket->PARKDATETIME->diffInSeconds($ticket->PARKOUTDATETIME) / 60);
    $daysParked = max(1, (int) ceil($minutesDiff / (24 * 60)));

    // 4. Handle Different Payment Modes
    // Use a clear conditional structure (if/else if) to handle the logic for each mode.
    $transactionDetails = [];

    if ($request->input('mode_of_payment') === 'cash') {
        // Handle cash payment logic if any is needed.
        // For now, this branch simply proceeds.
        $ticket->PARKFEE = (int) $daysParked * (float) $company->post_paid_rate;
        $ticket->save();

    } else if ($request->input('mode_of_payment') === 'card') {
        $qrCode = $request->input('qr_code');

        $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)->first();
        if (!$detail) {
            return back()->with('error', 'Invalid QR Code.');
        }

        if ($detail->balance <= 0) {
            return back()->with('error', 'Card already used up.');
        }

        if ($detail->balance < $daysParked) {
            return back()->with('error', 'Insufficient Balance.');
        }

        // Prepare card-specific details for the transaction
        $transactionDetails = [
            'detail' => $detail,
            'days_parked' => $daysParked,
        ];
    }

    // 5. Database Transaction for Atomicity
    // Perform all database operations inside a single transaction.
    DB::transaction(function () use ($ticket, $transactionDetails,$request) {
        
        $ticket->REMARKS = 'PAID';
        $ticket->ISPARKOUT = 1;
          $ticket->mode_of_payment = $request->input('mode_of_payment');
        $ticket->save();

        if (isset($transactionDetails['detail'])) {
            $detail = $transactionDetails['detail'];
            $daysParked = $transactionDetails['days_parked'];
            $detail->balance -= $daysParked;
            $detail->status = $detail->balance > 0 ? 'ACTIVE' : 'USED';
            $detail->save();
        }
    });

    // 6. Final Response
    // Use a single, clean return statement with optional flash data.
    $responseWith = [
        'success' => true,
        'ticket' => $ticket,
        'company' => $company,
    ];

    if (isset($transactionDetails['detail'])) {
        $detail = $transactionDetails['detail'];
        $daysParked = $transactionDetails['days_parked'];
        $responseWith['detail'] = $detail;
        $responseWith['message'] = 'Payment successful! Remaining balance: ' . $detail->balance . ' | Days parked: ' . $daysParked;
    }

    return redirect()->route('parkout.receipt')->with($responseWith);
}



public function processQrPayment(Request $request)
{
    $request->validate([
        'ticket_id' => 'required|exists:tickets,id',
        'qr_code'   => 'required|string',
    ]);

    $ticket = Ticket::find($request->ticket_id);
    $company = Company::find(1); 
    $qrCode = $request->qr_code;

 
    // $parkin = Carbon::parse($ticket->PARKDATETIME);
    // $parkout   = Carbon::parse($ticket->PARKOUTDATETIME);

    $minutesDiff = ceil($ticket->PARKDATETIME->diffInSeconds($ticket->PARKOUTDATETIME) / 60);
         // ab
    $daysParked  = max(1, (int) ceil($minutesDiff / (24 * 60))); // 1440 minutes = 1 day

    // 🔹 First check if QR exists at all
    $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)->first();
    if (!$detail) {
        return back()->with('error', 'Invalid QR Code.');
    }

     if ($detail->balance <= 0){
    return back()->with('error', 'Card already used up.');

    if ((int)$detail->balance < $daysParked) {
        return back()->with('error', 'Insufficient Balance. '.$ticket->balance);
    }

    // if ($ticket->ISPARKOUT){
    //        return back()->with('error', 'Car has been park out.');
    // }

   
}

    DB::transaction(function () use ($detail, $ticket, $daysParked) {
        $detail->balance -= $daysParked;
        $detail->status   = $detail->balance > 0 ? 'ACTIVE' : 'USED';
        $detail->save();

        $ticket->REMARKS  = 'PAID';
        $ticket->ISPARKOUT = 1;
        $ticket->save();
    });


   return redirect()->route('parkout.receipt')->with([
        'success' => true,
        'message' => 'Payment successful! Remaining balance: '.$detail->balance.' | Days parked: '.$daysParked,
        'ticket'  => $ticket,
        'detail'  => $detail,
        'company' => $company
   ]
    );

    // return redirect()->route('parkout.receipt')->with(
    //     'success',
    //     'Payment successful! Remaining balance: '.$detail->balance.' | Days parked: '.$daysParked
    // );
}



    public function parkout_receipt(){
        return inertia('Parkout/Receipt',[
            'ticket' => session('ticket'),
            'detail' => session('detail')
        ]);
    }






}