<?php

namespace App\Http\Controllers;
use App\Models\CardInventoryDetail;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiController extends Controller
{
    //

    public function search_card_number(Request $request){

        $query = $request->get('q', '');

        $cards = CardInventoryDetail::where('card_number', 'like', "%{$query}%")
                    ->where('status','AVAILABLE')
                    ->orderBy('card_number', 'desc')
                    ->limit(50)
                    ->get(['id', 'card_number','card_name','price']); // return only what you need

        return response()->json($cards);

    }



     public function todayTickets(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        return Ticket::whereDate('park_out_datetime', $date)
            ->where('is_park_out', 1)
            ->get();
    }
}
