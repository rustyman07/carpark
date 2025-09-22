<?php

namespace App\Http\Controllers;
use App\Models\CardInventoryDetail;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    //

    public function search_card_number(Request $request){

        $query = $request->get('q', '');

        $cards = CardInventoryDetail::where('card_number', 'like', "%{$query}%")
                    ->where('status','AVAILABLE')
                    ->limit(10)
                    ->get(['id', 'card_number','card_name','price']); // return only what you need

        return response()->json($cards);

    }
}
