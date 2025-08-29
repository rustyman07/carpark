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

        // 2. Create detail rows (without qr_code first)
        $details = [];
        for ($i = 0; $i < $request->no_of_cards; $i++) {
            $details[] = [
                'header_id'  => $inventory->id,
                'qr_code'    =>'test', // temporary placeholder
                'status'     => 'AVAILABLE',
                'card_name'  => $request->card_name,
                'no_of_days' => $request->no_of_days,
                'price'      => $request->price,
                'discount'   => $request->discount,
                'amount'     => $amount,
                'balance'    => 0.00,
                // 'createdby' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 3. Insert details and get their IDs
        CardInventoryDetail::insert($details);
        $year = date('Y');  


        // Fetch them back (IDs are now available)
        $insertedDetails = CardInventoryDetail::where('header_id', $inventory->id)->get();

        foreach ($insertedDetails as $detail) {
            $rawCode   =  $year.'00000' . $detail->id;
            $hashedCode = substr(hash('sha256', $rawCode), 0, 16); // shorter secure string

            $detail->update(['qr_code' =>  $hashedCode]);
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
