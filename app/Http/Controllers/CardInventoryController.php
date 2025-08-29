<?php

namespace App\Http\Controllers;
use App\Models\CardInventory;
use App\Models\CardTemplate;
use App\Models\CardInventoryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class CardInventoryController extends Controller
{
    //
     public function index()
    {

        $cardTemplate =  CardTemplate::where('cancelled',0)->get();
        $cardDetail = CardInventoryDetail::where('cancelled',0)->get();


           return inertia('CardInventory/Index',[
            'cardTemplate' => $cardTemplate,
            'cardDetail'   => $cardDetail
           ]);
    }

    // public function store(Request $request){
    //      try {
    //     DB::beginTransaction();

    //      $amount = $request->price - ($request->discount ?? 0);

    //     // 1. Create the inventory batch (header)
    //     $inventory = CardInventory::create([
    //         'card_name'    => $request->card_name,
    //         'no_of_cards' => $request->no_of_cards,
    //         'no_of_days'  => $request->no_of_days,
    //         'price'       => $request->price,
    //         'discount'    => $request->discount,
    //         'amount'      => $amount,
    //         // 'createdby'   => auth()->id(),
    //     ]);

    //     // 2. Prepare detail rows


    //     $details = [];
    //     for ($i = 0; $i < $request->no_of_cards; $i++) {
    //         $details[] = [
    //             'header_id' => $inventory->id,
    //             'qr_code'    => (string) Str::uuid(), // safe unique ID
    //             'status'    => 'AVAILABLE',
    //             'card_name'  => $request->card_name,
    //             'no_of_days'=> $request->no_of_days,
    //             'price'     => $request->price,
    //             'discount'  => $request->discount,
    //             'amount'    => $amount,
    //             'balance'   => 0,
    //             // 'createdby' => auth()->id(),
    //             'created_at'=> now(),
    //             'updated_at'=> now(),
    //         ];
    //     }

    //     // 3. Bulk insert details (faster than looping create())
    //     CardInventoryDetail::insert($details);

    //     DB::commit();

    //    $cardDetail = CardInventoryDetail::where('cancelled',0)->get();


    //     return redirect()->route('card-inventory.index')->with([
    //         'success' => 'Card Inventory with details created successfully!',
    //         //  'cardDetail' => $cardDetail
    //     ]);

    // } catch (\Throwable $e) {
    //     DB::rollBack();

    //     return redirect()->back()->with([
    //         'success' => false,
    //         'message' => 'Failed to create card inventory.',
    //         'error'   => $e->getMessage(),]);
    // }
    // }


public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $amount = $request->price - ($request->discount ?? 0);

        // 1. Create the inventory batch (header)
        $inventory = CardInventory::create([
            'card_name'   => $request->card_name,
            'no_of_cards' => $request->no_of_cards,
            'no_of_days'  => $request->no_of_days,
            'price'       => $request->price,
            'discount'    => $request->discount,
            'amount'      => $amount,
            // 'createdby'   => auth()->id(),
        ]);

        // 2. Create detail rows (placeholders)
        $details = [];
        for ($i = 0; $i < $request->no_of_cards; $i++) {
            $details[] = [
                'header_id'  => $inventory->id,
                'qr_code'  => 'temp', // placeholder for raw code
                'qr_code_hash' => 'temp', // placeholder for secure code
                'status'     => 'AVAILABLE',
                'card_name'  => $request->card_name,
                'no_of_days' => $request->no_of_days,
                'price'      => $request->price,
                'discount'   => $request->discount,
                'amount'     => $amount,
                'balance'    =>  $request->no_of_days,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 3. Insert details
        CardInventoryDetail::insert($details);
        $year = date('Y');  

        // 4. Fetch them back (IDs available now)
        $insertedDetails = CardInventoryDetail::where('header_id', $inventory->id)->get();

        foreach ($insertedDetails as $detail) {
            // predictable but internal code
            $qrCode   = $year . '1000' . $detail->id;

            // secure hash for external QR usage
            $hashedCode = substr(
                hash('sha256', $qrCode . Str::random(8)), // add randomness
                0,
                16
            );

            $detail->update([
                'qr_code'  => $qrCode,
                'qr_code_hash' => $hashedCode,
            ]);
        }

        DB::commit();

        return redirect()->route('card-inventory.index')->with([
            'success' => 'Card Inventory with details created successfully!',
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


}
