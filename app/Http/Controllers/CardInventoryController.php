<?php

namespace App\Http\Controllers;
use App\Models\CardInventory;
use App\Models\CardTemplate;
use App\Models\CardInventoryDetail;
use App\Models\Payment;
use App\Models\Company;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CardInventoryController extends Controller
{
    //
public function index(Request $request)
{

    $dateFrom   = $request->input('dateFrom', now()->startOfMonth()->toDateString());
    $dateTo      =  $request->input('dateTo', now()->toDateString());;
    $cardNumber  =  $request->input('card_number');
    $status      =  $request->input('status','All');

    $cardTemplate = CardTemplate::where('cancelled', 0)->get();

    $cardDetailQuery = CardInventoryDetail::where('cancelled', 0)
        ->orderBy('created_at', 'desc')
        ->orderBy('card_number', 'desc');

    // ✅ Add search by card number
    if (!empty($cardNumber)) {
        $cardDetailQuery->where('card_number', 'like', '%' . $cardNumber . '%');
    }

    if($status !== 'All'){
         $cardDetailQuery->where('status', $status);
    }

    if ($dateFrom && $dateTo) {
        $cardDetailQuery->whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo);
    } else {
        $cardDetailQuery->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
                        ->whereDate('created_at', '<=', now()->toDateString());
    }

    return inertia('CardInventory/Index', [
        'cardTemplate' => $cardTemplate,
        // 'cardDetail'   => $cardDetailQuery->paginate(10)->withQueryString()
        'cardDetail'   => $cardDetailQuery->get()
    ]);
}


 public function store(Request $request)
    {
        $data = $request->validate([
            'card_template_id' =>'required|integer',
            'card_name'   => 'required|string|max:255',
            'no_of_cards' => 'required|integer|min:1',
            'no_of_days'  => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
        ]);


        try {
            DB::beginTransaction();

            $amount = $request->price - ($request->discount ?? 0);
            
            $inventory = CardInventory::create([
                'card_template_id'=> $data['card_template_id'],
                'card_name'   => $data['card_name'],
                'no_of_cards' => $data['no_of_cards'],
                'no_of_days'  => $data['no_of_days'],
                'price'       => $data['price'],
                'discount'    => $data['discount'],
                'amount'      => $amount,
            ]);
            
            $details = [];
        
            $year = now()->year;
            $lastDetailId = CardInventoryDetail::max('id') ?? 0;
            
            for ($i = 0; $i < $request->no_of_cards; $i++) {


                $cardId = $lastDetailId + 1 + $i;
                $card_number = $year . sprintf('%05d', $cardId);
                $hashedCode = Hash::make($card_number);

                $details[] = [
                    'card_inventory_id'    => $inventory->id,
                    'card_template_id'=> $data['card_template_id'],
                    'card_number'  => $card_number,
                    'qr_code_hash' => $hashedCode,
                    'status'       => 'Available',
                    'card_name'    =>$data['card_name'],
                    'no_of_days'   =>  $data['no_of_days'],
                    'price'        =>  $data['price'],
                    'discount'     =>  $data['discount'],
                    'amount'       => $amount,
                    'balance'      => $data['price'],
                    'created_at'   => now(),
                    'created_by'   => Auth::id(),
                    'uuid'         => (string) Str::uuid()


                    // 'updated_at'   => now(),
                ];
            }

            //  Perform a single bulk insert
            CardInventoryDetail::insert($details);

            DB::commit();

             Cache::forget('dashboard.totalCards');
      
            return redirect()->route('card-inventory.index')->with([
                'success' => 'Cards  created successfully!',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with([
                'success' => false,
                'message' => 'Failed to create card inventory.',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function sell_card(){
             
        $cardTemplate = CardTemplate::where('cancelled', 0)->get();
      

        return Inertia('SellCards/Create',[
            'cardTemplate' => $cardTemplate,
              'scannedCards' => session('scannedCards')
        ]);
    }


public function scan_qr_cards(Request $request)
{
    if ($request->is_sell_card) {
        $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)
            ->where('status', 'Available')
            ->first();

        if (!$card) {
            return redirect()->back()->with(
                'error' , 'Invalid QR Code'
            );
        }  

    } else {
     $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)
        ->orWhere('card_number',  $request->qr_code)
        ->first();

        if (!$card) {
            return redirect()->back()->with(
                'error' , 'Invalid QR Code'
            );
        }

             if ($card->status == 'Consumed') {
            
            return redirect()->back()->with(
                'error', 'This card has already been consumed.',
            );
        }



        if ($card->status !== 'Confirmed') {
            
            return redirect()->back()->with(
                'error', 'This card is not yet confirmed. Please contact the administrator.',
            );
        }

      
    }



    if ($card->balance <= 0) {
        return redirect()->back()->with(
            'error', 'Insufficient balance',
        );
    }

    if (!$request->is_sell_card) {
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

        return redirect()
            ->route('show.payment', ['uuid' => $request->ticket_uuid])
            ->with('success', 'Card linked successfully');
    }

    // ✅ If selling card
    $scanned = session()->get("scanned_cards_payment", []);

    if (!array_key_exists($card->id, $scanned)) {
        $scanned[$card->id] = [
            'id'          => $card->id,
            'card_name'   => $card->card_name,
            'card_number' => $card->card_number,
            'balance'     => $card->balance,
            'price'       => $card->price ?? 0,
            'no_of_days'  => $card->no_of_days ?? 0,
        ];
    }

    session()->put("scanned_cards_payment", $scanned);


    return redirect()
        ->route('sell-card.create')
        ->with([
            'success' => 'Card scanned successfully!',
            'scannedCards' =>   $scanned[$card->id]
        ]);
        
}



// public function scan_qr_cards(Request $request)
// {
//     if ($request->is_sell_card) {
//         // 🟢 Selling card — must be Available
//         $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)
//             ->where('status', 'Available')
//             ->first();

//         if (!$card) {
//             return back()->with('error', 'Invalid QR Code');
//         }
//     } else {
//         // 🟢 Normal parking payment card — must be Confirmed
//         $card = CardInventoryDetail::where('qr_code_hash', $request->qr_code)->first();

//         if (!$card) {
//             return back()->with('error', 'Invalid QR Code');
//         }

//         if ($card->status !== 'Confirmed') {
//             return back()->with('error', 'This card is not yet confirmed. Please contact the administrator.');
//         }
//     }

//     // ❌ Insufficient balance
//     if ($card->balance <= 0) {
//         return back()->with('error', 'Insufficient balance');
//     }

//     if (!$request->is_sell_card) {
//         $ticketId = $request->ticket_id;
        
//      $scanned = session()->get('scanned_card');

//         // Replace any previously scanned card in session (single card only)
//         session()->put("scanned_card", [
//             'id'          => $card->id,
//             'card_number' => $card->card_number,
//             'balance'     => $card->balance,
//             'price'       => $card->price ?? 0,
//             'no_of_days'  => $card->no_of_days ?? 0,
//         ]);



//         return redirect()
//             ->route('show.payment', ['uuid' => $request->ticket_uuid])
//             ->with('success', 'Card linked successfully');
//     }


//     // ✅ If selling card
//     $scanned = session()->get("scanned_cards_payment", []);

//         $scanned = [
//             'id'          => $card->id,
//             'card_name'   => $card->card_name,
//             'card_number' => $card->card_number,
//             'balance'     => $card->balance,
//             'price'       => $card->price ?? 0,
//             'no_of_days'  => $card->no_of_days ?? 0,
//         ];
    

//     session()->put("scanned_cards_payment", $scanned);
 
//     return back()->with('success', 'Card scanned successfully!');


// }




public function sell_card_payment(Request $request)
{

    // dd( $request->payment_method);
    $data = $request->validate([
        'customer'      => 'required|string',
        'cash_amount' => 'required|numeric',
        'cards'       => 'nullable|array',
        'gcash_reference' => 'required|regex:/^[A-Za-z0-9]{8,15}$/',
        'payment_method'      => 'required|string',
        'cards.*'     => 'integer|exists:card_inventory_details,id',
    ]);

    //  Deduplicate cards array
    $cards = array_unique($data['cards'] ?? []);

    if (empty($cards)) {
        return back()->withErrors(['cards' => 'Please select or scan a card']);
    }

    //  Only allow cards not already sold
    $validCards = CardInventoryDetail::whereIn('id', $cards)
        ->where('status', '!=', 'Sold')
        ->get();

    if ($validCards->count() !== count($cards)) {
        return back()->withErrors(['cards' => 'Some cards are invalid or already sold.']);
    }

    //  Calculate total price
    $total_amount = $validCards->sum('price');

    //  Check cash amount
    if (!empty($data['cash_amount']) && $total_amount > $data['cash_amount']) {
        return back()->withErrors(['cash_amount' => 'Insufficient cash amount']);
    }

    try {
        DB::transaction(function () use ($validCards, $data, $total_amount) {
            $cash   = $data['cash_amount'] ?? $total_amount;
            $change = $cash - $total_amount;

            // create payment record
            $payment = Payment::create([
                'total_amount'  => $total_amount,
                'amount'        => $cash,
                'customer'       => $data['customer'],
                'change'        => $change,
                'status'        => 'Paid',
                'payment_type'  => 'Card',
                'payment_method' => $data['payment_method'],
                'gcash_reference' => $data['gcash_reference'] ?? null,
                'processed_by'   => Auth::id(),
                'paid_at'       => now(),
            ]);

            foreach ($validCards as $cardInventory) {
                // Update card status
                $cardInventory->status = 'Sold';
                $cardInventory->save();

                // Create payment detail
                $payment->details()->create([
                    'card_id'     => $cardInventory->id,
                    'card_number' => $cardInventory->card_number,
                    'qr_code'     => $cardInventory->qr_code,
                    'amount'      => $cardInventory->price,
                    'balance'     => $cardInventory->balance,
                    'card_name'   => $cardInventory->card_name,
                    'discount'    => $cardInventory->discount ?? 0,
                    'no_of_days'  => $cardInventory->no_of_days ?? 0,
                ]);
            }

        });

         Cache::forget('dashboard.revenueData');
         Cache::forget('dashboard.totalRevenue');


        return redirect(route('card-inventory.index'))->with('success', 'Payment recorded successfully!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Transaction failed: ' . $e->getMessage()]);
    }
}


public function transactions($card_id)
{
    $transactions = PaymentDetail::where('card_id', $card_id)
        ->whereHas('payment.ticket') // Only include if payment has a ticket
        ->with([
            'payment.ticket' => function ($query) {
                $query->select('id', 'plate_no', 'ticket_no');
            }
            
        ])
        ->get();

    return response()->json([
        'transactions' => $transactions,
    ]);
}

public function updateStatus(Request $request, $id)
{
    $card = CardInventoryDetail::findOrFail($id);

    if ($card->status === 'Confirmed') {
        return back()->with('error', 'This card is already confirmed.');
    }

    if ($card->status !== 'Sold') {
        return back()->with('error', 'Card status must be sold before confirming.');
    }

    $card->update([
        'status' => 'Confirmed',
        'valid_until' => now()->addYear(),
    ]);

    $cardDetail = CardInventoryDetail::latest()->get();

    return back()->with([
        'cardDetail' => $cardDetail,
        'success' => 'Card status updated successfully!',
    ]);
}




public function print_card($uuid)
{
  
    $card = CardInventoryDetail::where('uuid',$uuid)->firstOrFail();
    $company = Company::first();


    // Generate QR Code from card_number as SVG (no imagick needed)
    $qrCodeSvg = QrCode::format('svg')
        ->size(200)
        ->errorCorrection('H')
        ->generate($card->uuid);
    
    // Convert SVG to base64
    $qrCode = base64_encode($qrCodeSvg);

    // Get logo path
    $logoPath = public_path('images/comlogo.png');

    // Prepare data for the view
    $data = [
        'card' => $card,
        'company' => $company,
        'qrCode' => $qrCode,
        'logoPath' => $logoPath
    ];

    // Generate and stream PDF for thermal printer
    return Pdf::loadView('Printables.Card', $data)
        ->setPaper([0, 0, 226.77, 566.93], 'portrait') // 80mm width, auto height
        ->setOption('margin-top', 0)
        ->setOption('margin-right', 0)
        ->setOption('margin-bottom', 0)
        ->setOption('margin-left', 0)
        ->stream('card-' . $card->card_number . '.pdf');
}




}

