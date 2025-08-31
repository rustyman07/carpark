<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\Setting;
use App\Models\CardInventoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $ticket->update(['TICKETNO' => (string) (1000000 + $ticket->id)]);
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
    $data['PARKOUTDATETIME'] = isset(
        $data['PARKOUTYEAR'],
        $data['PARKOUTMONTH'],
        $data['PARKOUTDAY'],
        $data['PARKOUTHOUR'],
        $data['PARKOUTMINUTE'],
        $data['PARKOUTSECOND']
    ) ? \Carbon\Carbon::create(
        $data['PARKOUTYEAR'],
        $data['PARKOUTMONTH'],
        $data['PARKOUTDAY'],
        $data['PARKOUTHOUR'],
        $data['PARKOUTMINUTE'],
        $data['PARKOUTSECOND']
    ) : now();

    // 3️⃣ Fetch latest active ticket (business rule)
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
    $settings = Setting::find(1); 
    $ticket->PARKOUTDATETIME = $data['PARKOUTDATETIME']->format('Y-m-d H:i:s');

    $days = ($data['PARKOUTDAY'] - $ticket->PARKDAY) + 1;
    $ticket->PARKFEE = (int) $days * (float) $settings->FEE;

    // Update ticket
    $ticket->fill([
        'PARKOUTYEAR'   => $data['PARKOUTYEAR'],
        'PARKOUTMONTH'  => $data['PARKOUTMONTH'],
        'PARKOUTDAY'    => $data['PARKOUTDAY'],
        'PARKOUTHOUR'   => $data['PARKOUTHOUR'],
        'PARKOUTMINUTE' => $data['PARKOUTMINUTE'],
        'PARKOUTSECOND' => $data['PARKOUTSECOND'],
    ])->save();

    // 5️⃣ Success response
    return redirect()->route('parkout')->with([
        'ticket'  => $ticket,
        'success' => true,
    ]);
}
    public function submit_payment(Request $request){
        $data = $request->validate([
            'ID' => 'required|exists:tickets,id',
        ], [
            'ID.exists' => 'Invalid Payment.',
        ]);

        $ticket = Ticket::find($request->input('ID'));

                // Update ticket
        $ticket->fill([
            // 'REMARKS'   => 'PAID',
            'ISPARKOUT'  => 1,
            // 'PARKOUTBY'  => 
        ])->save();

        // return inertia('Parkout/Index', [
        //          'ticket'  => $ticket,
        //         'success' => true,
        //     ]);

// return back()->with([
//         'ticket' => $ticket,
//         'success' => true,
//     ]);

         return Inertia::render('Parkout/Index', [
        'ticket' => $ticket,
        // 'success' => true,
        'successMessage' => 'Payment successful!', // <-- pass actual string
    ]);

    }




// public function verifyQr(Request $request)
// {
//      $request->validate([
//         'ticket_id' => 'required|exists:tickets,id',
//         'qr_code'   => 'required|string',
//     ]);
//     $ticket = Ticket::find($request->ticket_id);
//     $qrCode = $request->qr_code;

//     $startDate = $ticket->PARKDAY; 
//     $today     = Carbon::today()->day;           

//     // $daysParked = $startDate->diffInDays($today) + 1; // +1 to count inclusive
//    $daysParked  =  ($today - $startDate) + 1;
  
//     $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)
//         ->where('balance', '>=',  $daysParked)
//         ->first();

//     // if (!$detail) {
//     //     return redirect()->back()->with('error', 'Insufficient Balance. Current Balance is: '.$daysParked);
//     // }
//     if (!$detail) {
//     return back()->withErrors([
//         'qr_code' => 'Insufficient Balance. Required: '.$daysParked
//     ]);
// }

//     // redirect to payment route, inertia will handle transition

    
//     return redirect()->route('submit.payment.qr', ['qr_code' => $qrCode]);
// }

// public function submit_payment_qrcode($qr_code)
// {
//     $detail = CardInventoryDetail::where('qr_code_hash', $qr_code)->first();

//     if (!$detail) {
//         return redirect()->back()->with('error', 'QR Code not found.');
//     }


//     // Do payment processing...
//     // $detail->balance - $daysParked;
//     $detail->status = 'USED';
//     $detail->save();

//     return redirect()->route('parkout')->with('success', 'Payment successful!');
// }

public function processQrPayment(Request $request)
{
    $request->validate([
        'ticket_id' => 'required|exists:tickets,id',
        'qr_code'   => 'required|string',
    ]);

    $ticket = Ticket::find($request->ticket_id);
    $qrCode = $request->qr_code;

    // ✅ Use NOW, not today (and round up by minutes → at least 1 day)
    $start = Carbon::parse($ticket->PARKDATETIME)->timezone(config('app.timezone'));
    $now   = now(config('app.timezone'));

    $minutesDiff = $start->diffInMinutes($now);          // absolute minutes
    $daysParked  = max(1, (int) ceil($minutesDiff / (24 * 60))); // 1440 minutes = 1 day

    // 🔹 First check if QR exists at all
    $detail = CardInventoryDetail::where('qr_code_hash', $qrCode)->first();
    if (!$detail) {
        return back()->with('error', 'Invalid QR Code.');
    }


    if ((int)$detail->balance < $daysParked) {
        return back()->with('error', 'Insufficient Balance. Days parked: '.$daysParked);
    }

    // if ($ticket->ISPARKOUT){
    //        return back()->with('error', 'Car has been park out.');
    // }

    if ($detail->balance == 0) {
    return back()->with('error', 'Card already used up.');
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