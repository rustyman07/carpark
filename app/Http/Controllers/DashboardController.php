<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\CardInventory;
use App\Models\CardInventoryDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 🚗 Active Parkings = tickets where park_out_datetime is NULL
        $activeParkings = Ticket::whereNull('park_out_datetime')
            ->where('cancelled', 0)
            ->count();

        // 💳 Total Cards Issued = count of all card inventory records
        $totalCards = CardInventoryDetail::count();

        // 💰 Total Revenue Today = sum of all payments made today
        $totalRevenueToday = Payment::whereDate('paid_at', today())->sum('total_amount');

        // 📊 Optional: revenue data for last 7 days (for chart)
        $revenueData = Payment::selectRaw('DATE(paid_at) as date, SUM(total_amount) as total')
            ->where('paid_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'total' => $row->total,
                ];
            });

        return Inertia::render('Dashboard/Index', [
            'activeParkings' => $activeParkings,
            'totalCards' => $totalCards,
            'totalRevenue' => $totalRevenueToday,
            'revenueData' => $revenueData,
        ]);
    }
}
