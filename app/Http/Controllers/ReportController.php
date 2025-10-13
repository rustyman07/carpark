<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Company;
use Carbon\Carbon;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function previewPDF(Request $request)
    {
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');

        $company = Company::first();

        // Convert to Carbon dates
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();

        $query = Ticket::whereBetween('park_out_datetime', [$from, $to])
            ->where('is_park_out', 1)
            ->where('remarks', 'Paid')
            ->whereNull('deleted_at');

        // Filter if cashier
        if ($user = Auth::user()) {
            if ($user->role === 2) {
                $query->where('park_out_by', $user->id);
            }
        }

        $tickets = $query->orderBy('park_out_datetime', 'desc')->get();


        $reportDate = $from->isSameDay($to)
            ? $from->format('F d, Y')
            : $from->format('F d, Y') . ' - ' . $to->format('F d, Y');

      
        $cashTotal = 0;
        $gcashTotal = 0;
        $cardTotal = 0;

        foreach ($tickets as $ticket) {
            $mode = strtolower($ticket->mode_of_payment ?? 'cash');
            $fee = (float)$ticket->park_fee;

            if ($mode === 'cash') $cashTotal += $fee;
            elseif ($mode === 'gcash') $gcashTotal += $fee;
            elseif ($mode === 'card') $cardTotal += $fee;
        }

        // Get logo as base64
        $logoBase64 = base64_encode(file_get_contents(public_path('images/comlogo.png')));

        $data = [
            'tickets' => $tickets,
            'totalParkFee' => $tickets->sum('park_fee'),
            'totalTickets' => $tickets->count(),
            'reportDate' => $reportDate,
            'company' => $company,
            'cashTotal' => $cashTotal,
            'gcashTotal' => $gcashTotal,
            'cardTotal' => $cardTotal,
            'logoBase64' => $logoBase64,
            'preparedBy' => $user ? $user->name : 'N/A',
        ];

        // Generate and stream PDF using Blade view
        return Pdf::loadView('Printables.ParkingReport', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->stream('parkout-report.pdf');
    }
}