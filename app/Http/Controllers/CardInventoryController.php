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
            
            // 1. Create the inventory batch (header)
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
                // 2. Generate the card number and hash *before* inserting
                // This assumes your ID generation logic is based on sequential numbers.
                // A better approach would be to get the next auto-increment ID from the database
                // but this is a quick fix based on your existing code.

                $cardId = $lastDetailId + 1 + $i;
                $card_number = $year . sprintf('%05d', $cardId);
                $hashedCode = Hash::make($card_number);

                $details[] = [
                    'card_inventory_id'    => $inventory->id,
                    'card_template_id'=> $data['card_template_id'],
                    'card_number'  => $card_number,
                    'qr_code_hash' => $hashedCode,
                    'status'       => 'AVAILABLE',
                    'card_name'    =>$data['card_name'],
                    'no_of_days'   =>  $data['no_of_days'],
                    'price'        =>  $data['price'],
                    'discount'     =>  $data['discount'],
                    'amount'       => $amount,
                    'balance'      => $data['price'],
                    'created_at'   => now(),
                    // 'updated_at'   => now(),
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

    public function sell_card(){
             
        $cardTemplate = CardTemplate::where('cancelled', 0)->get();

        return Inertia('SellCards/Create',[
            'cardTemplate' => $cardTemplate
        ]);
    }
public function show_no_available_cards($template_id)
{
    $Availblecard = CardInventoryDetail::where('card_template_id', $template_id)
    ->where('status','AVAILABLE')
    ->whereNull('deleted_at')
    ->count();

      

    return response()->json([
        'available_quantity' => $Availblecard
    ]);
}

}

