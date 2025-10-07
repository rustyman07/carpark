<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\CardInventoryDetail;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\CardsTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**S
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
        'plate_no'    => 'required|string|min:3',
        'park_year'   => 'nullable|integer',
        'park_month'  => 'nullable|integer',
        'park_day'    => 'nullable|integer',
        'park_hour'   => 'nullable|integer',
        'park_minute' => 'nullable|integer',
        'park_second' => 'nullable|integer',
    ]);



    // $ticketExists = Ticket::where('plate_no', $data['plate_no'])
    //     ->where(function ($q) {
    //         $q->whereIn('remarks', ['UNPAID'])
    //           ->orWhereNull('remarks');
    //     })->where('is_park_out', 0)
    //     ->whereNull('deleted_at')
    //     ->exists();


    $ticket = Ticket::where('plate_no', $data['plate_no'])
        ->whereNotNull('park_datetime')
        ->where('is_park_out', 0)
        ->whereNull('deleted_at')
        ->latest('park_datetime')   // order by park_datetime desc
        ->first();



        if ($ticket) {
    return back()->withErrors([
        'plate_no' => "This plate number already has an active ticket with a ticket no of {$ticket->ticket_no}.",
    ])->withInput();
    }

        // Build park_datetime from inputs (fallback: now)
        $park_datetime = isset($data['park_year'], $data['park_month'], $data['park_day'])
            ? Carbon::create(
                $data['park_year'],
                $data['park_month'],
                $data['park_day'],
                $data['park_hour']   ?? 0,
                $data['park_minute'] ?? 0,
                $data['park_second'] ?? 0
            )
            : now();

        $uuid = (string) Str::uuid();
        // Always sync fields
        $data['park_year']   = $park_datetime->year;
        $data['park_month']  = $park_datetime->month;
        $data['park_day']    = $park_datetime->day;
        $data['park_hour']   = $park_datetime->hour;
        $data['park_minute'] = $park_datetime->minute;
        $data['park_second'] = $park_datetime->second;
        $data['park_datetime'] = $park_datetime;
        $data['uuid'] = $uuid;

        // Defaults
        $data['cancelled'] = 0;
        $data['ticket_no'] = 0;
        $data['create_by'] =  Auth::id();
        $data['park_in_by'] =  Auth::id();
    

        DB::transaction(function () use ($data) {
            $ticket = Ticket::create($data);
        
            $ticket_no = '1' . sprintf('%06d', $ticket->id);
            $hash_ticket_no = Hash::make($ticket_no);

            $ticket->update([
                'ticket_no' =>      $ticket_no,     
                'qr_code'   => $hash_ticket_no,
                'remarks'  => 'Unpaid'
            ]);


            Cache::forget('dashboard.activeParkings');
            Cache::forget('dashboard.latestParkin');
        });



        // return back()->with('success', 'Ticket created successfully!');
        return redirect()->route('parkin.show',['uuid' => $uuid])->with('success', 'Ticket created successfully!');
    }


public function show(string $uuid)
{

    
    $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
    return inertia('Parkin/Show', [
        'ticket' => $ticket
    ]);
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
    $ticket = Ticket::where('id',$id)->where('is_park_out',0)->first();

    if(!$ticket){
        return back()->with('error','Cant be deleted ticket');
    }


    $ticket->delete(); // soft delete (sets deleted_at timestamp)

    return redirect()->back()->with('success', 'Ticket deleted successfully.');
    }

    // public function showLogs(Request $request)
    // {

    //     $type     = $request->input('type', 'PARK-IN'); // default
    //     $dateFrom = $request->input('dateFrom', now()->toDateString()); // default = today
    //    $dateTo   = $request->input('dateTo', now()->toDateString());   // default = today


    //     $query = Ticket::whereNull('deleted_at')
    //         ->where('is_park_out',$type ==='PARK-IN'? 0 : 1)
    //         ->select('id','ticket_no', 'plate_no', 'park_datetime', 'park_out_datetime','remarks','park_fee')
    //         ->orderByDesc('created_at');

    //     $dateColumn = $type === 'PARK-IN' ? 'park_datetime' : 'park_out_datetime';


    //     if ($dateFrom && $dateTo) {
    //         $query->whereDate($dateColumn, '>=', $dateFrom)
    //             ->whereDate($dateColumn, '<=', $dateTo);
    //     }

    //     return inertia('Logs/Index', [ 
    //    'Tickets' => $query->paginate(5)->withQueryString(), 

    //     ]);

    // }



//     public function showLogs(Request $request)
// {
//     $type     = $request->input('type', 'PARK-IN'); // default type
//     $dateFrom = $request->input('dateFrom', now()->toDateString()); // default = today
//     $dateTo   = $request->input('dateTo', now()->toDateString());   // default = today

//     $dateColumn = $type === 'PARK-IN' ? 'park_datetime' : 'park_out_datetime';

//     $tickets = Ticket::whereNull('deleted_at')
//         ->where('is_park_out', $type === 'PARK-IN' ? 0 : 1)
//         ->select('id', 'ticket_no', 'plate_no', 'park_datetime', 'park_out_datetime', 'remarks', 'park_fee')
//         ->whereBetween(DB::raw("DATE($dateColumn)"), [$dateFrom, $dateTo])
//         ->orderByDesc('created_at')
//         ->get(); // ✅ No pagination — load all records

//     return inertia('Logs/Index', [
//         'Tickets' => $tickets,
//         'filters' => [
//             'type' => $type,
//             'dateFrom' => $dateFrom,
//             'dateTo' => $dateTo,
//         ],
//     ]);
// }

public function showLogs(Request $request)
{

    // dd('dateFr' $request->$dateFrom  )
    $type     = $request->input('type', 'PARK-IN');
    $dateFrom = $request->input('dateFrom', now()->toDateString());
    $dateTo   = $request->input('dateTo', now()->toDateString());

    // ✅ Detect current shift automatically
    $now = now()->format('H:i:s');
    $shifts = [
        'MORNING' => ['06:00:00', '14:00:00'],
        'AFTERNOON' => ['14:00:01', '22:00:00'],
        'NIGHT' => ['22:00:01', '05:59:59'],
    ];

    $userShift = 'MORNING';
    $shiftStart = '06:00:00';
    $shiftEnd = '14:00:00';

    foreach ($shifts as $name => [$start, $end]) {
        if ($now >= $start && $now <= $end) {
            $userShift = $name;
            $shiftStart = $start;
            $shiftEnd = $end;
            break;
        }
    }

    $dateColumn = $type === 'PARK-IN' ? 'park_datetime' : 'park_out_datetime';

    $query = Ticket::whereNull('deleted_at')
        ->where('is_park_out', $type === 'PARK-IN' ? 0 : 1)
        ->whereDate($dateColumn, '>=', $dateFrom)
        ->whereDate($dateColumn, '<=', $dateTo);

    // ✅ Filter logic based on type
    if ($type === 'PARK-IN') {
        // Show active + park-ins during this shift
        $query->where(function ($q) use ($dateColumn, $shiftStart, $shiftEnd) {
            $q->whereTime($dateColumn, '>=', $shiftStart)
              ->whereTime($dateColumn, '<=', $shiftEnd)
              ->orWhereNull('park_out_datetime');
        });
    } else {
        // PARK-OUT view: Only show completed transactions within this shift
        $query->whereNotNull('park_out_datetime')
              ->whereTime($dateColumn, '>=', $shiftStart)
              ->whereTime($dateColumn, '<=', $shiftEnd);
    }

    $tickets = $query->select(
        'id',
        'ticket_no',
        'plate_no',
        'park_datetime',
        'park_out_datetime',
        'remarks',
        'park_fee'
    )->orderByDesc('created_at')->get();

    // ✅ Total only from completed park-outs within shift
    $totalParkFee = Ticket::whereNull('deleted_at')
        ->where('remarks','Paid')
        ->whereNotNull('park_out_datetime') // only completed transactions
        ->whereBetween('park_out_datetime', [
            now()->toDateString() . ' ' . $shiftStart,
            now()->toDateString() . ' ' . $shiftEnd,
        ])
        ->sum('park_fee');

    return inertia('Logs/Index', [
        'Tickets' => $tickets,
        'totalParkFee' => $totalParkFee,
        'filters' => [
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'shift' => $userShift,
            'shiftStart' => $shiftStart,
            'shiftEnd' => $shiftEnd
        ]
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

   session()->forget('scanned_cards');

    // 1️⃣ Validate input
    // $data = $request->validate([
    //     if($request->input('ISSCANQR' == true)){
    //         'qr_code' => 'required|string|exists:tickets,qr_code',
    //     } else {     
    //             'plate_no'       => 'required|string|exists:tickets,plate_no',       
    //     }
    
    //     'park_out_year'   => 'nullable|integer',
    //     'park_out_month'  => 'nullable|integer',
    //     'park_out_day'    => 'nullable|integer',
    //     'park_out_hour'   => 'nullable|integer',
    //     'park_out_minute' => 'nullable|integer',
    //     'park_out_second' => 'nullable|integer',
    // ], [
    //     'plate_no.required' => 'Plate number is required',
    //     'plate_no.exists'   => 'Plate number not found',
    // ]);

      $rules = [
        'park_out_year'   => 'nullable|integer',
        'park_out_month'  => 'nullable|integer',
        'park_out_day'    => 'nullable|integer',
        'park_out_hour'   => 'nullable|integer',
        'park_out_minute' => 'nullable|integer',
        'park_out_second' => 'nullable|integer',
    ];

 
    if ($request->boolean('is_scan_qr')) {
        $rules['qr_code'] = 'required|string|exists:tickets,qr_code';
    } else {
        $rules['plate_no'] = 'required|string|exists:tickets,plate_no';
    }

    $data = $request->validate($rules, [
        'plate_no.required' => 'Plate number is required',
        'plate_no.exists'   => 'Plate number not found',
        'qr_code.required'  => 'QR code is required',
        'qr_code.exists'    => 'Invalid QR code',
    ]);
    


    // 2️⃣ Compute park_out_datetime (use provided or fallback to now)
  $park_out_datetime = isset($data['park_out_year'], $data['park_out_month'], $data['park_out_day'])
    ? Carbon::create(
        $data['park_out_year'],
        $data['park_out_month'],
        $data['park_out_day'],
        $data['park_out_hour']   ?? 0,
        $data['park_out_minute'] ?? 0,
        $data['park_out_second'] ?? 0
    )
    : now();

        $data['park_out_year']   = $park_out_datetime->year;
        $data['park_out_month']  = $park_out_datetime->month;
        $data['park_out_day']    = $park_out_datetime->day;
        $data['park_out_hour']   = $park_out_datetime->hour;
        $data['park_out_minute'] = $park_out_datetime->minute;
        $data['park_out_second'] = $park_out_datetime->second;
        $data['park_out_datetime'] = $park_out_datetime;
       

    //  $ticket = Ticket::where('plate_no', $data['plate_no'])
    //      ->where('remarks',0)
    //     ->latest('park_datetime')
    //     ->first();

        if ($request->boolean('is_scan_qr')) {
            $ticket = Ticket::where('qr_code', $data['qr_code'])
                ->WhereNull('deleted_at')
                ->first();


                    
        if (!$ticket) {
            return redirect()->back()->with([
                'error' => 'Ticket not found for this QR code.',
                'success' => false
            ]);
        }

    

        } else {

                $ticket = Ticket::where('plate_no', $data['plate_no'])
                ->where(function ($q) {
                    $q->whereIn('remarks', ['UNPAID'])
                    ->orWhereNull('remarks');
                })
                 ->WhereNull('deleted_at')
                ->latest('park_datetime')
                ->first();

                        
                if (!$ticket){
                    return redirect()->back()->with([ 
                        'error' => "Hasn't Park in Yet",
                        'success' => false
                
                ]);
                }

        }


    
    // 4️⃣ Calculate fee
    $company = Company::find(1); 
    $start = Carbon::parse($ticket->park_datetime)->timezone(config('app.timezone')); 
    $end =     Carbon::parse( $data['park_out_datetime'])->timezone(config('app.timezone'));



    $minutesDiff =  $start->diffInMinutes($end);


// $rate = null;

// if ($company->rate == 'perhour') {
//     // Simple per hour rate
 
//     $rate = $ratePerhour;

// } elseif ($company->rate == 'perday') {
//     // Simple per day rate
//     $rate =  $ratePerDay ;

// } else {
//     // Base = 1 full day
//     $rate = $ratePerDay ;
//    if ($hoursParked >=24){
//   $hours = $daysParked * 24;
//   $remainingHours =  $hoursParked - $hours;
//   $rate = $daysParked * (float) $company->rate_perday + $remainingHours * (float) $company->rate_perhour;
//   $hoursParked = $remainingHours;

    

//    }

// }

//  $start =     Carbon::parse('2025-09-15 10:42:08'); 
//   $end  =     Carbon::parse('2025-09-19 10:42:10');
// Use Carbon's diffInMinutes for the most precise duration
// $minutesParked = $start->diffInMinutes($end);
// $minutesParked = floor($start->diffInSeconds($end) / 60);

// $rate = 0;
// $daysParked = 0;
// $remainingMinutes = 0;
// $hoursParked = 0;

// if ($company->rate == 'perhour') {

//     // Per-hour billing (still rounds up to minimum 1 hour)
//     $hoursParked = max(1, ceil($minutesParked / 60));
//     $rate = $hoursParked * (float) $company->rate_perhour;

// } elseif ($company->rate == 'perday') {

//     // ✅ Always charge at least 1 day even if less than 24h
//     $daysParked = max(1, ceil($minutesParked / 1440));
//     $rate = $daysParked * (float) $company->rate_perday;

// } else { // ✅ combination logic

//     if ($minutesParked <= 1440) {
//         // ✅ Less than or equal to 1 day → charge full 1 day rate
//         $rate = (float) $company->rate_perday;
//     } else {
//         // ✅ More than 1 day → count full days + hourly for remainder
//         $daysParked = floor($minutesParked / 1440);
//         $remainingMinutes = $minutesParked % 1440;

//         $rate = $daysParked * (float) $company->rate_perday;

//         if ($remainingMinutes > 0) {
//             $hoursParked = ceil($remainingMinutes / 60);
//             $rate += $hoursParked * (float) $company->rate_perhour;
//         }
//     }
// }


$company = Company::find(1); 

$start = Carbon::parse($ticket->park_datetime)->timezone(config('app.timezone')); 
$end   = Carbon::parse($data['park_out_datetime'])->timezone(config('app.timezone'));


$minutesDiff = (int) $start->diffInMinutes($end);

$ratePerHour = (float) $company->rate_perhour;
$ratePerDay  = (float) $company->rate_perday;


$hourly_limit   = (int) $company->hourly_billing_limit * 60;
$freeMinutes    = (int) $company->grace_minutes;


$rate = 0;
$daysParked = 0;
$hoursParked = 0;
$remainingMinutes = 0;

if ($company->rate == 'perhour') {
   
    $hoursParked = max(1, ceil($minutesDiff / 60));
    $rate = $hoursParked * $ratePerHour;

} elseif ($company->rate == 'perday') {

    $daysParked = max(1, ceil($minutesDiff / 1440));
    $rate = $daysParked * $ratePerDay;
 

} else {
   

    if ($minutesDiff <= $hourly_limit + $freeMinutes) {

        $hoursParked = max(1, floor($minutesDiff / 60));

        if ($minutesDiff > 60){

            if ($minutesDiff >  ($hoursParked * 60) + $freeMinutes )
                  $hoursParked = max(1, ceil($minutesDiff / 60));

         }

        // $hoursParked = min($hoursParked, $hourly_limit);
       // $totalmin = ($hoursParked * 60) + $freeMinutes;

        $rate = $hoursParked * $ratePerHour;
      //  $hoursParkedciel = max(1, ceil($minutesDiff / 60));

      //dd(['dif' =>$minutesDiff, 'total' => $hourly_limit + $freeMinutes]);

    } elseif ($minutesDiff <= 1440) {

        $rate = $ratePerDay;
        $hoursParked = ceil($minutesDiff / 60); 
              //dd(['second' =>$hoursParked]);
    } else {

        $daysParked = floor($minutesDiff / 1440);
        $remainingMinutes = $minutesDiff % 1440;

        $rate = $daysParked * $ratePerDay;

        if ($remainingMinutes > 0) {
            $hoursParked = ceil($remainingMinutes / 60);
            $rate += $ratePerDay;
                 // dd(['third' =>$hoursParked]);
        }

         $rate = $daysParked * $ratePerDay + ($remainingMinutes > 0 ? $ratePerDay : 0);
    }
}



    $ticket->park_fee =  $rate;

    $ticket->fill([
        'is_park_out'     => 1,
        'park_out_year'   => $data['park_out_year'],
        'park_out_month'  => $data['park_out_month'],
        'park_out_day'    => $data['park_out_day'],
        'park_out_hour'   => $data['park_out_hour'],
        'park_out_minute' => $data['park_out_minute'],
        'park_out_second' => $data['park_out_second'],
        'total_minutes'  => $minutesDiff,
        'days_parked'   => $daysParked,
        'hours_parked'  => $hoursParked,
        'park_out_datetime' => $end,
        'park_out_by'     =>  Auth::id()
     ])->save();

   
    Cache::forget('dashboard.latestParkout');
    return redirect()->route('show.payment', [
        'uuid' => $ticket->uuid
    ]);
}


public function show_payment(string $uuid)
{
    $ticket   = Ticket::where('uuid', $uuid)->firstOrFail();
    $ticketId = $ticket->id;


        if ($ticket->remarks === 'PAID') {
        return redirect()->route('parkout');
    }

    $scannedCards = session()->get("scanned_cards.$ticketId", []);

    $remainingFee = $ticket->park_fee;
    $totalCovered = 0;
    $processedCards = [];

    foreach ($scannedCards as $card) {
        if ($remainingFee <= 0) {
         
            $processedCards[] = [
                'id'               => $card['id'],
                'card_number'      => $card['card_number'],
                'no_of_days'      => $card['no_of_days'],
                'price'            => $card['price'],
                'balance'          => $card['balance'],
                'covered'          => 0,
                'remainingBalance' => $card['balance'],
            ];
            // return redirect()->back()->with('success','cards are enough to cover');
            continue;
        }

        // Deduct as much as possible from this card
        $covered = min($card['balance'], $remainingFee);
        $remainingFee -= $covered;
        $totalCovered += $covered ;


        $processedCards[] = [
            'id'               => $card['id'],
            'card_number'      => $card['card_number'],
            'no_of_days'      => $card['no_of_days'],
            'price'            => $card['price'],
            'balance'          => $card['balance'],
            'covered'          => $covered,
            'remainingBalance' => $card['balance'] - $covered,
        ];
    }

    $cashNeeded = max(0, $remainingFee);

    return Inertia('Parkout/Payment', [
        'ticket'       => new TicketResource($ticket),
        'scannedCards' => $processedCards,
        'totalCovered' => $totalCovered,
        'cashNeeded'   => $cashNeeded,
    ]);
}

public function submit_payment(Request $request)
{
    $data = $request->validate([
        'ticket_id'   => 'required|exists:tickets,id',
        'cash_amount' => 'nullable|numeric|min:0', // optional, only needed if cards don't cover
        'cards'       => 'nullable|array',         // optional array of card IDs
    ]);

    $ticket  = Ticket::findOrFail($request->ticket_id);
    $company = Company::find(1);

    if ($ticket->remarks === 'Paid') {
        return redirect()->route('parkin.index')
                         ->with(['error' => 'This ticket has already been paid']);
    }

    $cards       = $data['cards'] ?? [];
    $totalPaid   = 0;
    $amountToPay = $ticket->park_fee ?? 0;
    $payment     = null;

    try {
        DB::transaction(function () use ($ticket, $cards, $data, &$payment, &$totalPaid, &$amountToPay, $request) {

            // Determine payment method
            $ticket->remarks = 'Paid';
            $ticket->mode_of_payment = count($cards) ? 'card' : 'cash';
            $ticket->save();

            // Create payment header
            $payment = Payment::create([
                'ticket_id'      => $ticket->id,
                'ticket_no'      => $ticket->ticket_no,
                'days_deducted'  => $ticket->days_parked ?? 0,
                'payment_type'   => 'ticket',
                'payment_method' => $ticket->mode_of_payment,
                'status'         => 'paid',
                'paid_at'        => now(),
            ]);

            // Deduct progressively from cards
            foreach ($cards as $cardId) {
                if ($amountToPay <= 0) break;

                $cardInventory = CardInventoryDetail::findOrFail($cardId);

                $deduct = min($cardInventory->balance, $amountToPay);
                $cardInventory->balance -= $deduct;
                if ($cardInventory->balance <= 0) {
                    $cardInventory->status = 'Consumed';
                }
                $cardInventory->save();

                $payment->details()->create([
                    'card_id'     => $cardInventory->id,
                    'card_number' => $cardInventory->card_number,
                    'qr_code'     => $cardInventory->qr_code,
                    'amount'      => $deduct,
                    'balance'     => $cardInventory->balance,
                    'card_name'   => $cardInventory->card_name,
                    'discount'    => $cardInventory->discount ?? 0,
                    'no_of_days'  => $cardInventory->no_of_days ?? 0,
                ]);

                $amountToPay -= $deduct;
                $totalPaid += $deduct;
            }

            // Remaining fee is cash
            if ($amountToPay > 0) {
                $cashProvided = $data['cash_amount'] ?? 0;

                if ($cashProvided < $amountToPay) {
                    throw new \Exception('Insufficient Amount.');
                }

                if ($cards){
                            $payment->details()->create([
                    'card_id'     => null,
                    'card_number' => null,
                    'qr_code'     => null,
                    'amount'      => $amountToPay,
                    'balance'     => null,
                    'card_name'   => 'Cash',
                    'discount'    => 0,
                    'no_of_days'  => 0,
                ]);

                }
        
            }

            $change = ($data['cash_amount'] ?? 0) - $amountToPay;

            $payment->update([
                'amount'       => $data['cash_amount'] ?? 0.00,
                'total_amount' => $totalPaid + $amountToPay,
                'change'       => $change,
            ]);
        });

        // Clear scanned cards from session
        session()->forget('scanned_cards');
     
         Cache::forget('dashboard.revenueData');
         Cache::forget('dashboard.totalRevenue');

        // Redirect with success
        return redirect()->route('parkout.receipt', ['id' => $ticket->uuid])
                         ->with([
                             'success' => 'Payment successful!',
                             'id'      => $ticket->uuid,
                             'company' => $company,
                             'cards'   => $cards,
                         ]);

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
}



    public function parkout_receipt(Request $request){

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


}