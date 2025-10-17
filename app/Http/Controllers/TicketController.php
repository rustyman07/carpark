<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\User;
use App\Models\CardInventoryDetail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;


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




        return redirect()->route('parkin.show',['uuid' => $uuid]);
    }


    public function show(string $uuid)
    {

        
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        return inertia('Parkin/Show', [
            'ticket' => $ticket
        ]);
    }


    public function print_ticket($uuid)
{
     $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
    $company = Company::first();

    // Generate QR Code as base64
   $qrCodeSvg = QrCode::format('svg')
        ->size(200)
        ->errorCorrection('H')
        ->generate($ticket->qr_code);
    
    // Convert SVG to base64
    $qrCode = base64_encode($qrCodeSvg);

    // Get logo path
    $logoPath = public_path('images/comlogo.png');

    // Prepare data for the view
    $data = [
        'ticket' => $ticket,
        'company' => $company,
        'qrCode' => $qrCode,
        'logoPath' => $logoPath
    ];

    // Generate and stream PDF for thermal printer
    return Pdf::loadView('Printables.Ticket', $data)
        ->setPaper([0, 0, 226.77, 566.93], 'portrait') // 80mm width, auto height
        ->setOption('margin-top', 0)
        ->setOption('margin-right', 0)
        ->setOption('margin-bottom', 0)
        ->setOption('margin-left', 0)
        ->stream('ticket-' . $ticket->ticket_no . '.pdf');
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

    public function showLogs(Request $request)
{
    $user = Auth::user();


    $staff = User::where('deleted_at', null)
        ->where('role', 2)
        ->get();

    $type     = $request->input('type', 'PARK-IN');
    $dateFrom = $request->input('dateFrom', now()->toDateString());
    $dateTo   = $request->input('dateTo', now()->toDateString());
    $request->input('staff', 'All');

    $tickets = Ticket::whereNull('deleted_at')
        ->where('is_park_out', $type === 'PARK-IN' ? 0 : 1)
        ->with('parkOutUser:id,name') // 👈 eager load only needed fields
        ->select(
            'id',
            'ticket_no',
            'plate_no',
            'park_datetime',
            'park_out_datetime',
            'remarks',
            'park_fee',
            'park_out_by'
        );
    $dateColumn = $type === 'PARK-IN' ? 'park_datetime' : 'park_out_datetime';

    if ($dateFrom && $dateTo) {
        $tickets->whereDate($dateColumn, '>=', $dateFrom)
                ->whereDate($dateColumn, '<=', $dateTo);
    }


    if ($type === 'PARK-OUT' && $user->role == 2) {
        $tickets->where('park_out_by', $user->id);
    }
    
    if ($type === 'PARK-OUT' && $user->role != 2 && $request->filled('staff') && $request->input('staff') != 'All') {
        $tickets->where('park_out_by',(int) $request->input('staff'));
    }
    

    return inertia('Logs/Index', [
        'Tickets' => $tickets->orderByDesc('created_at')->get(),
        'filters' => [
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'staff' => $staff
        ]
    ]);
}



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


//     $user = auth()->user();
//     $isAdmin = $user->role == 1;

//     // ✅ Default values
//     $shiftStart = now()->startOfDay();
//     $shiftEnd = now()->endOfDay();
//     $userShift = 'FULL DAY';

//     // ✅ If not admin → use actual shift log
//     if (!$isAdmin) {
//         $shiftLog = ShiftLog::where('user_id', $user->id)
//             ->where(function ($q) {
//                 // ✅ Still active shift (no end yet)
//                 $q->whereNull('ended_at')
//                 // ✅ OR ended today
//                 ->orWhereDate('ended_at', now()->toDateString())
//                 // ✅ OR started today (for normal daytime shifts)
//                 ->orWhereDate('started_at', now()->toDateString());
//             })
//             ->latest()
//             ->first();

//         if ($shiftLog) {
//             $shiftStart = $shiftLog->started_at;
//             $shiftEnd = $shiftLog->ended_at ?? now();
//             $userShift = $shiftLog->shift_name ?? 'CURRENT SHIFT';
//         } else {
//             $userShift = 'NO ACTIVE SHIFT';
//         }
//     }

//         if ($isAdmin) {
//             // Admin sees total for whole day
//             $totalParkFee = Ticket::whereNull('deleted_at')
//                 ->where('remarks', 'Paid')
//                 ->whereNotNull('park_out_datetime')
//                 ->whereBetween('park_out_datetime', [
//                     now()->startOfDay(),
//                     now()->endOfDay(),
//                 ])
//                 ->sum('park_fee');
//         } else {
//             // Staff sees total only for their current shift
//             $totalParkFee = Ticket::whereNull('deleted_at')
//                 ->where('remarks', 'Paid')
//                 ->where('park_out_by', $user->id)
//                 ->whereNotNull('park_out_datetime')
//                 ->whereBetween('park_out_datetime', [$shiftStart, $shiftEnd])
//                 ->sum('park_fee');
//         }


//         return inertia('Logs/Index', [
//             'Tickets' => $tickets,
//             'totalParkFee' => $totalParkFee,
//             'filters' => [
//                 'type' => $type,
//                 'dateFrom' => $dateFrom,
//                 'dateTo' => $dateTo,
//                 'shift' => $userShift,
//                 'shiftStart' => $shiftStart,
//                 'shiftEnd' => $shiftEnd
//             ]
//         ]);
// }

// public function showLogs(Request $request)
// {
//     $type     = $request->input('type', 'PARK-IN');
//     $dateFrom = $request->input('dateFrom', now()->toDateString());
//     $dateTo   = $request->input('dateTo', now()->toDateString());

//     $user = auth()->user();
//     $isAdmin = $user->role == 1;

//     // ✅ Default values
//     $shiftStart = now()->startOfDay();
//     $shiftEnd = now()->endOfDay();
//     $userShift = 'FULL DAY';

//     // ✅ If not admin → use actual shift log
//     if (!$isAdmin) {

        
//         $shiftLog = ShiftLog::where('user_id', $user->id)
//             ->whereDate('started_at', now()->toDateString())
//             ->latest()
//             ->first();

//         if ($shiftLog) {
//             $shiftStart = $shiftLog->started_at;
//             $shiftEnd = $shiftLog->ended_at ?? now();
//             $userShift = $shiftLog->shift_name ?? 'CURRENT SHIFT';
//         } else {
//             $userShift = 'NO ACTIVE SHIFT';
//         }
//     }

//     $dateColumn = $type === 'PARK-IN' ? 'park_datetime' : 'park_out_datetime';

//     // ✅ Build query
//     $query = Ticket::whereNull('deleted_at')
//         ->where('is_park_out', $type === 'PARK-IN' ? 0 : 1)
//         ->whereBetween($dateColumn, [$shiftStart, $shiftEnd]);

//     if ($type === 'PARK-IN') {
//         $query->where(function ($q) use ($dateColumn, $shiftStart, $shiftEnd) {
//             // ✅ Always include active cars (still parked), no matter when they entered
//             $q->whereNull('park_out_datetime')
//             // ✅ Also include PARK-INs during this shift
//             ->orWhere(function ($q2) use ($dateColumn, $shiftStart, $shiftEnd) {
//                 $q2->whereTime($dateColumn, '>=', $shiftStart)
//                     ->whereTime($dateColumn, '<=', $shiftEnd);
//             });
//         });
//     }
//     else {
//         $query->whereNotNull('park_out_datetime');
//     }

//     $tickets = $query->select(
//         'id',
//         'ticket_no',
//         'plate_no',
//         'park_datetime',
//         'park_out_datetime',
//         'remarks',
//         'park_fee'
//     )->orderByDesc('created_at')->get();

//     // ✅ Total park fee calculation based on role
//     if ($isAdmin) {
//         // Admin sees total for whole day
//         $totalParkFee = Ticket::whereNull('deleted_at')
//             ->where('remarks', 'Paid')
//             ->whereNotNull('park_out_datetime')
//             ->whereBetween('park_out_datetime', [
//                 now()->startOfDay(),
//                 now()->endOfDay(),
//             ])
//             ->sum('park_fee');
//     } else {
//         // Staff sees total only for their current shift
//         $totalParkFee = Ticket::whereNull('deleted_at')
//             ->where('remarks', 'Paid')
//             ->whereNotNull('park_out_datetime')
//             ->whereBetween('park_out_datetime', [$shiftStart, $shiftEnd])
//             ->sum('park_fee');
//     }

//     return inertia('Logs/Index', [
//         'Tickets' => $tickets,
//         'totalParkFee' => $totalParkFee,
//         'filters' => [
//             'type' => $type,
//             'dateFrom' => $dateFrom,
//             'dateTo' => $dateTo,
//             'shift' => $userShift,
//             'shiftStart' => $shiftStart,
//             'shiftEnd' => $shiftEnd
//         ]
//     ]);
// }










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

             $existingUnpaid = Ticket::where('plate_no', $data['plate_no'])
        ->where('remarks', 'unpaid')
        ->whereNotNull('park_out_datetime')
        ->whereNull('deleted_at')
        ->latest('park_out_datetime')
        ->first();

            if ($existingUnpaid) {
                // ⚠️ Already has unpaid record — skip updating, use current record instead
                return redirect()->route('show.payment', [
                    'uuid' => $existingUnpaid->uuid
                ])->with([
                    'info' => 'Existing unpaid record found. Using that record instead.',
                    'success' => true
                ]);
            }


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





$company = Company::find(1);

$start = Carbon::parse($ticket->park_datetime)->timezone(config('app.timezone'));
$end   = Carbon::parse($data['park_out_datetime'])->timezone(config('app.timezone'));

// ✅ Use seconds for precise calculation, then round up to the next full minute
$minutesDiff = (int) ceil($start->diffInSeconds($end) / 60);

$ratePerHour = (float) $company->rate_perhour;
$ratePerDay  = (float) $company->rate_perday;

$hourly_limit = (int) $company->hourly_billing_limit * 60; // e.g., 10 hours * 60
$freeMinutes  = (int) $company->grace_minutes;             // e.g., 20

$rate = 0;
$daysParked = 0;
$hoursParked = 0;
$remainingMinutes = 0;

if ($company->rate == 'perhour') {

    $hoursParked = max(1, ceil($minutesDiff / 60));
    $rate = $hoursParked * $ratePerHour;

} elseif ($company->rate == 'perday') {

    $fullDays = floor($minutesDiff / 1440);        // full 24-hour days
    $remainingMinutes = $minutesDiff % 1440;       // leftover minutes

    // Apply grace period
    if ($remainingMinutes > $freeMinutes) {
        $daysParked = $fullDays + 1;               // extra day
    } else {
        $daysParked = max(1, $fullDays);           // at least 1 day
    }

    $hoursParked = $remainingMinutes <= $freeMinutes ? 0 : ceil($remainingMinutes / 60); // display only
    $rate = $daysParked * $ratePerDay;

} else {
    // Combined rate logic
    if ($minutesDiff <= $hourly_limit) {
        // Charge hourly
        $hoursParked = max(1, ceil($minutesDiff / 60));
        $rate = $hoursParked * $ratePerHour;
   

    } elseif ($minutesDiff <= 1440) {

        $daysParked = 1;
        $hoursParked = 0;
        $rate = $ratePerDay;
         

    } else {
        // More than 1 day
        $daysParked = floor($minutesDiff / 1440);
        $remainingMinutes = $minutesDiff % 1440;
        $rate = $daysParked * $ratePerDay;

        if ($remainingMinutes > $freeMinutes) {
            // Remaining minutes exceed grace period → add 1 full day
            $daysParked += 1;
            $rate += $ratePerDay;
            // $hoursParked = ceil($remainingMinutes / 60);
        } else {
            $hoursParked = 0;
        }
    }
}

$ticket->park_fee = $rate;


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
    $uuid = $ticket->id;


        if ($ticket->remarks === 'PAID') {
        return redirect()->route('parkout');
    }

    $scannedCards = session()->get("scanned_cards.$uuid", []);

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
        'cash_amount' => 'nullable|numeric|min:0', 
        'gcash_amount' => 'nullable|numeric|min:0',
        'cards'       => 'nullable|array',         
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

      
             $ticket->remarks = 'Paid';

            // Updated mode_of_payment logic
            $cashAmount  = $data['cash_amount'] ?? 0;
            $gcashAmount = $data['gcash_amount'] ?? 0;

            if (!empty($cards) && ($cashAmount + $gcashAmount) > 0) {
                $ticket->mode_of_payment = 'mixed';
            } elseif (!empty($cards)) {
                $ticket->mode_of_payment = 'card';
            } elseif (!empty($gcashAmount)) {
                $ticket->mode_of_payment = 'gcash';
            } else {
                $ticket->mode_of_payment = 'cash';
            }

            $ticket->save();
          
            $payment = Payment::create([
                'ticket_id'      => $ticket->id,
                'ticket_no'      => $ticket->ticket_no,
                'days_deducted'  => $ticket->days_parked ?? 0,
                'payment_type'   => 'ticket',
                'payment_method' => $ticket->mode_of_payment,
                'status'         => 'paid',
                'processed_by'    => Auth::id(),
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


           
                // Check if total non-card payment covers remaining fee
                if (($cashAmount + $gcashAmount) < $amountToPay) {
                    throw new \Exception('Insufficient Amount.');
                }

                // Applied from cash/GCash
                $appliedAmount = $amountToPay;

                // Change only from cash overpayment
                $change = max(0, $cashAmount - $appliedAmount);

                // Update Payment table
                $payment->update([
                    'amount'          => $cashAmount + $gcashAmount,
                    'total_amount'    => $totalPaid + $appliedAmount,
                    'Gcash_reference' => $data['Gcash_reference'] ?? null,
                    'change'          => $change,
                ]);



           
        });

        // Clear scanned cards from session
        session()->forget('scanned_cards');
     
         Cache::forget('dashboard.revenueData');
         Cache::forget('dashboard.totalRevenue');

        // Redirect with success
        return redirect()->route('receipt.index', ['id' => $ticket->uuid])
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




}