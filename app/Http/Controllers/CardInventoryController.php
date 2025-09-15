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

        $dateFrom = $request->input('dateFrom');
        $dateTo   = $request->input('dateTo');


        $cardTemplate = CardTemplate::where('cancelled', 0)->get();
           $cardDetailQuery = CardInventoryDetail::where('cancelled', 0)
        ->orderBy('created_at', 'desc'); // order by latest first


        // Apply a filter only if date parameters exist in the request.
        if ($dateFrom && $dateTo) {
            $cardDetailQuery->whereDate('created_at', '>=', $dateFrom)
                            ->whereDate('created_at', '<=', $dateTo);
        } else {
            // If no dates are provided, show data for the last 30 days by default.
            $cardDetailQuery->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
                            ->whereDate('created_at', '<=', now()->toDateString());
        }

        return inertia('CardInventory/Index', [
            'cardTemplate' => $cardTemplate,
            'cardDetail'   => $cardDetailQuery->paginate(10)->withQueryString()
        ]);
    }




// public function store(Request $request)
// {
//     try {
//         DB::beginTransaction();

//         $amount = $request->price - ($request->discount ?? 0);

//         // 1. Create the inventory batch (header)
//         $inventory = CardInventory::create([
//             'card_name'   => $request->card_name,
//             'no_of_cards' => $request->no_of_cards,
//             'no_of_days'  => $request->no_of_days,
//             'price'       => $request->price,
//             'discount'    => $request->discount,
//             'amount'      => $amount,
//             // 'createdby'   => auth()->id(),
//         ]);

//         // 2. Create detail rows (placeholders)
//         $details = [];
//         for ($i = 0; $i < $request->no_of_cards; $i++) {
//             $details[] = [
//                 'header_id'  => $inventory->id,
//                 'card_number'  => 'temp_' . $i,   // temporary placeholder
//                 'qr_code_hash' => 'temp_' . $i,  // temporary placeholder
//                 'status'     => 'AVAILABLE',
//                 'card_name'  => $request->card_name,
//                 'no_of_days' => $request->no_of_days,
//                 'price'      => $request->price,
//                 'discount'   => $request->discount,
//                 'amount'     => $amount,
//                 'balance'    =>  $request->no_of_days,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ];
//         }

//         // 3. Insert details
//         CardInventoryDetail::insert($details);
//         $year = date('Y');  

//         // 4. Fetch them back (IDs available now)
//         $insertedDetails = CardInventoryDetail::where('header_id', $inventory->id)->get();

//         foreach ($insertedDetails as $detail) {
//             // predictable but internal code
//           //  $card_number   = $year . '1000' . $detail->id;

//             $card_number =  $year . sprintf('%05d', $detail->id);

//             $hashedCode = Hash::make($card_number);

//             $detail->update([
//                 'card_number'  => $card_number,
//                 'qr_code_hash' => $hashedCode,
//             ]);
//         }

//         DB::commit();

//         return redirect()->route('card-inventory.index')->with([
//             'success' => 'Card Inventory with details created successfully!',
//         ]);

//     } catch (\Throwable $e) {
//         DB::rollBack();

//         return redirect()->back()->with([
//             'success' => false,
//             'message' => 'Failed to create card inventory.',
//             'error'   => $e->getMessage(),
//         ]);
//     }
// }

 public function store(Request $request)
    {
        $data = $request->validate([
            'card_name'   => 'required|string|max:255',
            'no_of_cards' => 'required|integer|min:1',
            'no_of_days'  => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
        ]);


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
            ]);
            
            $details = [];
        
            $year = now()->year;
            $lastDetailId = CardInventoryDetail::max('id') ?? 0;
            
            for ($i = 0; $i < $request->no_of_cards; $i++) {
                // 2. Generate the card number and hash *before* inserting
                // This assumes your ID generation logic is based on sequential numbers.
                // A better approach would be to get the next auto-increment ID from the database
                // but this is a quick fix based on your existing code.

                $cardId = $lastDetailId + 1 + $i;
                $card_number = $year . sprintf('%05d', $cardId);
                $hashedCode = Hash::make($card_number);

                $details[] = [
                    'header_id'    => $inventory->id,
                    'card_number'  => $card_number,
                    'qr_code_hash' => $hashedCode,
                    'status'       => 'AVAILABLE',
                    'card_name'    => $request->card_name,
                    'no_of_days'   => $request->no_of_days,
                    'price'        => $request->price,
                    'discount'     => $request->discount,
                    'amount'       => $amount,
                    'balance'      => $request->price,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            // 3. Perform a single bulk insert
            CardInventoryDetail::insert($details);

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
