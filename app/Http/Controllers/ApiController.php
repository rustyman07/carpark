<?php

namespace App\Http\Controllers;
use App\Models\CardInventoryDetail;
use App\Models\Payment;
use App\Models\Ticket;

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



      public function search(Request $request)
    {
        // Get inputs (with defaults)
        $dateFrom = $request->input('dateFrom', now()->toDateString());
        $dateTo   = $request->input('dateTo', now()->toDateString());
        $type     = $request->input('type'); // "Card" | "Ticket" | "All" | null

        // Normalize full-day range using Carbon
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to   = Carbon::parse($dateTo)->endOfDay();

        // Build query
        $query = Payment::with(['ticket', 'details', 'user'])
            ->whereBetween('created_at', [$from, $to]);

        // Filter by type if provided (and not 'All')
        if ($type && strtolower($type) !== 'all') {
            $query->where('payment_type', $type);
        }

        // Execute query
        $payments = $query->orderBy('created_at', 'desc')->get();

        // Return JSON response
        return response()->json([
            'success' => true,
            'count' => $payments->count(),
            'data' => $payments,
        ]);
    }


     public function todayTickets(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        return Ticket::whereDate('park_out_datetime', $date)
            ->where('is_park_out', 1)
            ->get();
    }
}
