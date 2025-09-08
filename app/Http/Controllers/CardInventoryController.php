<?php

namespace App\Http\Controllers;
use App\Models\CardInventory;
use App\Models\CardTemplate;
use App\Models\CardInventoryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class CardInventoryController extends Controller
{
    //
     public function index(Request $request)
    {
        
        $dateFrom = $request->input('dateFrom', now()->toDateString()); // default = today
        $dateTo   = $request->input('dateTo', now()->toDateString());   // default = today



        $cardTemplate =  CardTemplate::where('cancelled',0)->get();
        $cardDetail = CardInventoryDetail::where('cancelled',0);

        
                if ($dateFrom && $dateTo) {
            $cardDetail->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo);
        };




           return inertia('CardInventory/Index',[
            'cardTemplate' => $cardTemplate,
            'cardDetail'   => $cardDetail->paginate(10)->withQueryString()
           ]);
    }




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
                'card_number'  => 'temp_' . $i,   // temporary placeholder
                'qr_code_hash' => 'temp_' . $i,  // temporary placeholder
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
          //  $qrCode   = $year . '1000' . $detail->id;

            $qrCode =  $year . sprintf('%05d', $detail->id);

            $hashedCode = Hash::make($qrCode);

            $detail->update([
                'card_number'  => $qrCode,
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
