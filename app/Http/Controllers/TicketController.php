<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\CardInventoryDetail;
use App\Models\PaymentDetail;

use App\Models\CardsTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  
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
        'PLATENO'    => 'required|string|min:3',
        'PARKYEAR'   => 'nullable|integer',
        'PARKMONTH'  => 'nullable|integer',
        'PARKDAY'    => 'nullable|integer',
        'PARKHOUR'   => 'nullable|integer',
        'PARKMINUTE' => 'nullable|integer',
        'PARKSECOND' => 'nullable|integer',
    ]);



    // $ticketExists = Ticket::where('PLATENO', $data['PLATENO'])
    //     ->where(function ($q) {
    //         $q->whereIn('REMARKS', ['UNPAID'])
    //           ->orWhereNull('REMARKS');
    //     })->where('ISPARKOUT', 0)
    //     ->whereNull('deleted_at')
    //     ->exists();



    $ticket = Ticket::where('PLATENO', $data['PLATENO'])
        ->whereNotNull('PARKDATETIME')
        ->where('ISPARKOUT', 0)
        ->whereNull('deleted_at')
        ->latest('PARKDATETIME')   // order by PARKDATETIME desc
        ->first();



        if ($ticket) {
    return back()->withErrors([
        'PLATENO' => "This plate number already has an active ticket with a ticket no of {$ticket->TICKETNO}.",
    ])->withInput();
}

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

    $uuid = (string) Str::uuid();
    // Always sync fields
    $data['PARKYEAR']   = $parkDateTime->year;
    $data['PARKMONTH']  = $parkDateTime->month;
    $data['PARKDAY']    = $parkDateTime->day;
    $data['PARKHOUR']   = $parkDateTime->hour;
    $data['PARKMINUTE'] = $parkDateTime->minute;
    $data['PARKSECOND'] = $parkDateTime->second;
    $data['PARKDATETIME'] = $parkDateTime;
    $data['uuid'] = $uuid;

    // Defaults
    $data['CANCELLED'] = 0;
   $data['TICKETNO'] = 0;
   

    DB::transaction(function () use ($data,) {
        $ticket = Ticket::create($data);
     
        $ticketno = '1' . sprintf('%06d', $ticket->id);
        $hash_ticketno = Hash::make($ticketno);

        $ticket->update([
            'TICKETNO' =>      $ticketno,     
            'QRCODE'   => $hash_ticketno,
        ]);
    });


    // return back()->with('success', 'Ticket created successfully!');
    return redirect()->route('parkin.show',['uuid' => $uuid])->with('success', 'Ticket created successfully!');
}

    /**
     * Display the specified resource.
     */
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
    $ticket = Ticket::findOrFail($id);
    $ticket->delete(); // soft delete (sets deleted_at timestamp)

    return redirect()->back()->with('success', 'Ticket deleted successfully.');
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

   session()->forget('scanned_cards');

    // 1️⃣ Validate input
    // $data = $request->validate([
    //     if($request->input('ISSCANQR' == true)){
    //         'QRCODE' => 'required|string|exists:tickets,QRCODE',
    //     } else {     
    //             'PLATENO'       => 'required|string|exists:tickets,PLATENO',       
    //     }
    
    //     'PARKOUTYEAR'   => 'nullable|integer',
    //     'PARKOUTMONTH'  => 'nullable|integer',
    //     'PARKOUTDAY'    => 'nullable|integer',
    //     'PARKOUTHOUR'   => 'nullable|integer',
    //     'PARKOUTMINUTE' => 'nullable|integer',
    //     'PARKOUTSECOND' => 'nullable|integer',
    // ], [
    //     'PLATENO.required' => 'Plate number is required',
    //     'PLATENO.exists'   => 'Plate number not found',
    // ]);

      $rules = [
        'PARKOUTYEAR'   => 'nullable|integer',
        'PARKOUTMONTH'  => 'nullable|integer',
        'PARKOUTDAY'    => 'nullable|integer',
        'PARKOUTHOUR'   => 'nullable|integer',
        'PARKOUTMINUTE' => 'nullable|integer',
        'PARKOUTSECOND' => 'nullable|integer',
    ];

 
    if ($request->boolean('is_scan_qr')) {
        $rules['qr_code'] = 'required|string|exists:tickets,QRCODE';
    } else {
        $rules['PLATENO'] = 'required|string|exists:tickets,PLATENO';
    }

    $data = $request->validate($rules, [
        'PLATENO.required' => 'Plate number is required',
        'PLATENO.exists'   => 'Plate number not founds',
        'qr_code.required'  => 'QR code is required',
        'qr_code.exists'    => 'Invalid QR code',
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

        if ($request->boolean('is_scan_qr')) {
            $ticket = Ticket::where('QRCODE', $data['qr_code'])
                ->WhereNull('deleted_at')
                ->first();

    

        } else {

                $ticket = Ticket::where('PLATENO', $data['PLATENO'])
                ->where(function ($q) {
                    $q->whereIn('REMARKS', ['UNPAID'])
                    ->orWhereNull('REMARKS');
                })
                 ->WhereNull('deleted_at')
                ->latest('PARKDATETIME')
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
    $start = Carbon::parse($ticket->PARKDATETIME)->timezone(config('app.timezone'));
        // $start         =             Carbon::parse('2025-09-16 23:22:55'); 
    $end =     Carbon::parse( $data['PARKOUTDATETIME'])->timezone(config('app.timezone'));
     $end =     Carbon::parse('2025-09-16 23:23:42');


     //$minutesDiff = floor($start->diffInSeconds($end) / 60);
//      $minutesDiff = $start->diffInMinutes($end);          // absolute minutes

//     $rate = null;
//     $daysParked = null;
//     $hoursParked = null;
//     $hoursParked = (int) ceil($minutesDiff / 60);
//     // $hoursParked = max(1, (int) ceil($minutesDiff / 60));
//     $daysParked = (int) ceil($minutesDiff / 1440);
//     $daysParked = max(1, $daysParked);


//     if ($company->rate == 'perhour') {
//         $rate = (int) $hoursParked * (float) $company->rate_perhour;
//      }
//     elseif ($company->rate == 'perday') {
//     $rate = (int) $daysParked * (float) $company->rate_perday;
//     }
//     else {
//          // The base rate is always the full day rate.
//     $rate = (float) $company->rate_perday ;
//       $daysParked = max(1, ($daysParked -1));
    
// //    dd(['rate'=> $rate,'hours'=>$hoursParked] );

//     // Check for any additional days (more than 24 hours).
//     if ($hoursParked > 24) {
//         // Calculate the number of full days after the first day.
//         $additionalDays = floor(($hoursParked - 24) / 24);

//         // Calculate the remaining hours after accounting for additional days.
//         $remainingHours = ($hoursParked - 24) % 24;
        
//         // Add the rate for the additional full days.
//         $rate += $additionalDays * (float) $company->rate_perday;

//         // If there are any remaining hours, bill them.
//         if ($remainingHours > 0) {
//             $rate += ceil($remainingHours) * (float) $company->rate_perhour;
//         }
//         // $hoursParked = $remainingHours;
//     }


        
    //   $daysParked = max(1,($daysParked -1) ) ;
    //   if ($daysParked == 1){
      
    //     if ($hoursParked  > 24)
    //          $hoursParked  -= ($daysParked * 24);
    //     else
    //         $hoursParked = 0;  
    //     }

    //   else{
    //  $hoursParked  -= ($daysParked * 24);
    //   }

    // $rate = ((int) $daysParked* (float) $company->rate_perday)
    //           + ((int)  $hoursParked  * (float) $company->rate_perhour);


    $minutesDiff =  $start->diffInMinutes($end); // absolute minutes

$hoursParked = (int) ceil($minutesDiff / 60);   // round UP to next hour
$daysParked  = (int) ceil($minutesDiff / 1440); // round UP to next day
$daysParked = max(1, $daysParked);


$rate = null;

if ($company->rate == 'perhour') {
    // Simple per hour rate
 
    $rate = $hoursParked * (float) $company->rate_perhour;

} elseif ($company->rate == 'perday') {
    // Simple per day rate
    $rate = $daysParked * (float) $company->rate_perday;

} else {
    // Base = 1 full day
   $rate =  $company->rate_perday;
   $daysParked = max(1,($daysParked - 1));
    // $hoursParked = (int) floor($minutesDiff / 60);
    // if ($daysParked = 1){

    // }


    // if ($hoursParked >= 24) {
    //     // Additional full days (after the first 24h)
    //     $additionalDays = floor(($hoursParked - 24) / 24);
    //     $rate += $additionalDays * (float) $company->rate_perday;

    //     // Remaining hours after full days
    //     $remainingHours = ($hoursParked - 24) % 24;

    //     if ($remainingHours > 0) {
    //         $rate += $remainingHours * (float) $company->rate_perhour;
    //     }

    //     $hoursParked = $remainingHours;
    //      dd(['mindiff' => $minutesDiff, 'hoursparkedssss' => $hoursParked,]);
    // }
    

    // dd(['mindiff' => $minutesDiff, 'hoursparked' => $hoursParked,]);
}


    //     // }else
    //     //     $rate = ((int) $daysParked * (float) $company->rate_perday);
    

    $ticket->PARKFEE =  $rate;

    $ticket->fill([
        'ISPARKOUT'     => 1,
        'REMARKS'       => 'UNPAID',
        'PARKOUTYEAR'   => $data['PARKOUTYEAR'],
        'PARKOUTMONTH'  => $data['PARKOUTMONTH'],
        'PARKOUTDAY'    => $data['PARKOUTDAY'],
        'PARKOUTHOUR'   => $data['PARKOUTHOUR'],
        'PARKOUTMINUTE' => $data['PARKOUTMINUTE'],
        'PARKOUTSECOND' => $data['PARKOUTSECOND'],
        'TOTALMINUTES'  => $minutesDiff,
        'days_parked'   => $daysParked,
        'hours_parked'  => $hoursParked,
        'PARKOUTDATETIME' => $end
    ])->save();


//     Payment::create([
//     'ticket_id'      => $ticket->id,
//     'amount'         => $ticket->PARKFEE, // initial calculated fee
//     'payment_type'   => 'ticket',
// ]);

    // 5️⃣ Success response
   
    return redirect()->route('show.payment', [
        'uuid' => $ticket->uuid
    ]);
}


public function show_payment(string $uuid)
{
    $ticket   = Ticket::where('uuid', $uuid)->firstOrFail();
    $ticketId = $ticket->id;


        if ($ticket->REMARKS === 'PAID') {
        return redirect()->route('parkout');
    }

    $scannedCards = session()->get("scanned_cards.$ticketId", []);

    $remainingFee = $ticket->PARKFEE;
    $totalCovered = 0;
    $processedCards = [];

    foreach ($scannedCards as $card) {
        if ($remainingFee <= 0) {
            // No more fee to cover
            $processedCards[] = [
                'card_number'      => $card['card_number'],
                'price'            => $card['price'],
                'balance'          => $card['balance'],
                'covered'          => 0,
                'remainingBalance' => $card['balance'],
            ];
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

public function scan_qr_cards(Request $request)
{
    $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)->first();

    if (!$card) {
        return back()->withErrors(['qr_code' => 'Invalid QR Code']);
    }

    if ($card->balance <= 0) {
        return back()->with(['error' =>'Insufficient balance']);
    }

    $ticketId = $request->ticket_id;
    $scanned = session()->get("scanned_cards.$ticketId", []);

    if (!array_key_exists($card->id, $scanned)) {
        $scanned[$card->id] = [
            'id'          => $card->id,
            'card_number' => $card->card_number,
            'balance'     => $card->balance,
            'price'       => $card->price ?? 0,
            'no_of_days'  => $card->no_of_days ?? 0,
        ];
    }

    session()->put("scanned_cards.$ticketId", $scanned);

    // ✅ Redirect to show_payment route, not just "back"
return redirect()->route('show.payment', ['uuid' => $request->ticket_uuid])
    ->with('success', 'Card linked successfully');
}




// public function scan_qr_cards(Request $request)
// {
//     $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)->first();

//     if (!$card) {
//         return back()->withErrors(['qr_code' => 'Invalid QR Code']);
//     }

//     if ($card->balance <= 0) {
//         return back()->with(['error' => 'Insufficient balance']);
//     }

//     $ticketId = $request->ticket_id;
//     $ticket   = Ticket::findOrFail($ticketId);

//     // Save card in session (by ticket_id)
//     $scanned = session()->get("scanned_cards.$ticketId", []);

//     // Avoid duplicates
//     if (!array_key_exists($card->id, $scanned)) {
//         $scanned[$card->id] = [
//             'id'          => $card->id,
//             'card_number' => $card->card_number,
//             'balance'     => $card->balance,
//             'price'       => $card->price ?? 0,
//             'no_of_days'  => $card->no_of_days ?? 0,
//         ];
//         session()->put("scanned_cards.$ticketId", $scanned);
//     }

//     // Recalculate totals
//     $coverage = $this->calculateCoverage($scanned, $ticket->PARKFEE ?? 0);

//     return back()->with([
//         'success'      => 'Card linked successfully',
//         'card'         => $card,
//         'scannedCards' => array_values($coverage['cards']),
//         'totalCovered' => $coverage['totalCovered'],
//         'cashNeeded'   => $coverage['cashNeeded'],
//     ]);
// }


public function submit_payment(Request $request)
{

    $data = $request->validate([
        'ticket_id' => 'required|exists:tickets,id',
        // 'cash_amount' => 'required|numeric',
    ]);




    $ticket  = Ticket::findOrFail($request->ticket_id);
    $company = Company::find(1);



    if ($ticket->REMARKS === 'PAID') {

       return redirect()->route('parkin.index')->with(['error' =>'This ticket has already been paid']);
    }

    // dd($request->all());

    $cards = $request->cards ?? []; // should just be an array of IDs in order
    $totalPaid = 0;

    $amountToPay = $ticket->PARKFEE ?? 0;

    $payment = null;

    DB::transaction(function () use ($ticket, $cards, $request, &$payment, $data, &$totalPaid,$amountToPay) {
        // Mark ticket as paid
        $ticket->REMARKS         = 'PAID';
        $ticket->mode_of_payment = $request->mode_of_payment ?? 'card';
        $ticket->save();

        // Create header payment
        $payment = Payment::create([
            'ticket_id'      => $ticket->id,
            'ticket_no'      => $ticket->TICKETNO,
            'days_deducted'  => $ticket->days_parked ?? 0,
            'payment_type'   => 'ticket',
            'payment_method' => $request->mode_of_payment ?? 'card',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        // Remaining fee
    
       

        // Deduct progressively from cards
        foreach ($cards as $cardId) {
            if ($amountToPay <= 0) {
                break; // already covered
            }

        $cardInventory = CardInventoryDetail::where('id', $cardId)->first();
            if (!$cardInventory) {
                continue; // skip if not found
            }

            // Deduct only what this card can cover
            $deduct = min($cardInventory->balance, $amountToPay);

            // Update card balance
            $cardInventory->balance -= $deduct;
            if ($cardInventory->balance <= 0) {
                $cardInventory->status = 'CONSUMED';
            }
            $cardInventory->save();

            // Record payment detail using relation
            $payment->details()->create([
                'card_id'      => $cardInventory->id,
                'card_number'  => $cardInventory->card_number,
                'qr_code'      => $cardInventory->qr_code,
                'amount'       => $deduct,
                'balance'      => $cardInventory->balance,
                'card_name'    => $cardInventory->card_name,
                'discount'     => $cardInventory->discount ?? 0,
                'no_of_days'   => $cardInventory->no_of_days ?? 0,
            ]);

            // Reduce remaining fee
            $amountToPay -= $deduct ;
            $totalPaid += $deduct ;
    

            // dd([
            //     'amountToPay' => $amountToPay,
            //     'totalPaid'   => $totalPaid,
            // ]);


        }

        // If not fully covered → mark remainder as cash
        if ($amountToPay > 0) {
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

        $payment->update(['amount' => $totalPaid + $request->cash_amount ?? 0.00]);
    });





    $responseWith = [
        'success' => 'Payment successful!',
        'id'      => $ticket->uuid,
        'company' => $company,
        'cards'   => $cards, // just IDs you sent
        
        
    ];

     session()->forget('scanned_cards');

    return redirect()
        ->route('parkout.receipt', [
            'id' => $ticket->uuid
        ])
        ->with($responseWith);
}

// public function submit_payment(Request $request)
// {
//     $validationRules = [
//         'ticket_id' => 'required|exists:tickets,id',
//     ];

//     $request->validate($validationRules);

//     $ticket  = Ticket::findOrFail($request->ticket_id);
//     $company = Company::find(1);

//     if ($ticket->REMARKS === 'PAID') {
//         return back()->with('error', 'This ticket has already been paid.');
//     }

//     // Pull cards from session (not just request)
//     $scannedCards = session()->get("scanned_cards.$ticket->id", []);

//     // Run coverage calculation
//     $coverage = $this->calculateCoverage($scannedCards, $ticket->PARKFEE );

//     DB::transaction(function () use ($ticket, $coverage, $request) {
//         // Mark ticket as paid
//         $ticket->REMARKS         = 'PAID';
//         $ticket->mode_of_payment = $request->mode_of_payment ?? 'card';
//         $ticket->save();

//         // Create header payment
//         $payment = Payment::create([
//             'ticket_id'      => $ticket->id,
//             'ticket_no'      => $ticket->TICKETNO,
//             'amount'         => $ticket->PARKFEE ?? 0.00,
//             'days_deducted'  => $request->days_parked ?? 0,
//             'payment_type'   => 'ticket',
//             'payment_method' => $request->mode_of_payment ?? 'card',
//             'status'         => 'paid',
//             'paid_at'        => now(),
//         ]);

//         // Apply deductions to each card
//         foreach ($coverage['cards'] as $cardData) {
//             if ($cardData['covered'] <= 0) {
//                 continue; // skip cards that didn’t cover anything
//             }

//             $cardInventory = CardInventoryDetail::find($cardData['id']);
//             if (!$cardInventory) {
//                 continue;
//             }

//             // Update card balance
//             $cardInventory->balance = $cardData['remainingBalance'];
//             if ($cardInventory->balance <= 0) {
//                 $cardInventory->status = 'CONSUMED';
//             }
//             $cardInventory->save();

//             // Record payment detail
//             $payment->details()->create([
//                 'card_id'     => $cardInventory->id,
//                 'card_number' => $cardInventory->card_number,
//                 'qr_code'     => $cardInventory->qr_code,
//                 'amount'      => $cardData['covered'],
//                 'balance'     => $cardData['remainingBalance'],
//                 'card_name'   => $cardInventory->card_name,
//                 'discount'    => $cardInventory->discount ?? 0,
//                 'no_of_days'  => $cardInventory->no_of_days ?? 0,
//             ]);
//         }

//         // If still not fully covered → add Cash record
//         if ($coverage['cashNeeded'] > 0) {
//             $payment->details()->create([
//                 'card_id'     => null,
//                 'card_number' => null,
//                 'qr_code'     => null,
//                 'amount'      => $coverage['cashNeeded'],
//                 'balance'     => null,
//                 'card_name'   => 'Cash',
//                 'discount'    => 0,
//                 'no_of_days'  => 0,
//             ]);
//         }
//     });

//     // Clear session cards after payment
//     session()->forget("scanned_cards.$ticket->id");

//     $responseWith = [
//         'success' => 'Payment successful!',
//         'id'      => $ticket->uuid,
//         'company' => $company,
//         'cards'   => $coverage['cards'],
//     ];

//     return redirect()
//         ->route('parkout.receipt', ['id' => $ticket->uuid])
//         ->with($responseWith);
// }





    public function parkout_receipt(Request $request){

        $ticket = Ticket::where('uuid',$request->id)->first();
        if(!$ticket){
                //  return back()->with('error', 'Insufficient Balance.');
                 abort(404, 'Ticket not found');
        }
      

         $payment = Payment::where('ticket_id',$ticket->id)->first();

            $balance = $payment->balance;
    
        // if ($ticket->mode_of_payment ==='card'){
        //    $card = CardInventoryDetail::where('qr_code_hash',$ticket->QRCODE)->first();
        //    if(!$card){
        //     //  return back()->with('error', 'Invalid QR Code.');
        //       abort(404, 'Invalid QR Code');
        //    }
        //    $balance = $card?->balance;
        // }
   

        $company = Company::findOrFail(1);

        return Inertia('Parkout/Receipt',[
            'ticket' => (new TicketResource($ticket))->resolve(),
            'balance' => $balance,
            'company' => $company,
            'amount'  => $payment?->amount ?? 0.00,
            
        ]);
    }



    private function calculateCoverage(array $scannedCards, float $fee): array
{
    $remainingFee   = $fee;
    $totalCovered   = 0;
    $processedCards = [];

    foreach ($scannedCards as $card) {
        if ($remainingFee <= 0) {
            $processedCards[] = [
                'card_number'      => $card['card_number'],
                'balance'          => $card['balance'],
                'covered'          => 0,
                'remainingBalance' => $card['balance'],
            ];
            continue;
        }

        $covered = min($card['balance'], $remainingFee);
        $remainingFee -= $covered;
        $totalCovered += $covered;

        $processedCards[] = [
            'id'               => $card['id'],
            'card_number'      => $card['card_number'],
            'balance'          => $card['balance'],
            'covered'          => $covered,
            'remainingBalance' => $card['balance'] - $covered,
        ];
    }

    return [
        'cards'       => $processedCards,
        'totalCovered'=> $totalCovered,
        'cashNeeded'  => max(0, $remainingFee),
    ];
}





}