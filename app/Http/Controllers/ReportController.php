<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// class ReportController extends Controller
// {
//     public function previewPDF(Request $request)
//     {
//         $dateFrom = $request->query('dateFrom');
//         $dateTo = $request->query('dateTo');

//         $company = Company::first();

//         $from = Carbon::parse($dateFrom)->startOfDay();
//         $to = Carbon::parse($dateTo)->endOfDay();

//     $query = Ticket::whereBetween('tickets.park_out_datetime', [$from, $to]) 
//         ->where('tickets.is_park_out', 1)
//         ->where('tickets.remarks', 'Paid')
//         ->whereNull('tickets.deleted_at')
//         ->leftJoin('payments', 'payments.ticket_id', '=', 'tickets.id')
//         ->select('tickets.*', 'payments.total_amount');


//             if ($user = Auth::user()) {
//                 if ($user->role === 2) {
//                     $query->where('park_out_by', $user->id);
//                 } elseif ($user->role != 2 && $request->filled('staff') && $request->input('staff') != 'All') {
//                     $query->where('park_out_by', (int) $request->input('staff'));
//                 }
//             }

//         $tickets = $query->with('payment')->orderBy('park_out_datetime', 'desc')->get();

//             // Group tickets by park_out_by (staff member)
//             $groupedTickets = $tickets->groupBy('park_out_by')->map(function ($group) {
//                 $staffId = $group->first()->park_out_by;
//                 $staff = User::find($staffId);
                
//                 return [
//                     'staff_id' => $staffId,
//                     'staff_name' => $staff ? $staff->name : 'Unknown',
//                     'tickets' => $group,
//                     'total' => $group->sum(fn($t) => $t->payment->total_amount ?? 0),
//                     'count' => $group->count(),
//                 ];
//             });

//             $reportDate = $from->isSameDay($to)
//                 ? $from->format('F d, Y')
//                 : $from->format('F d, Y') . ' - ' . $to->format('F d, Y');

//             $cashTotal = 0;
//             $gcashTotal = 0;
//             $cardTotal = 0;

//             foreach ($tickets as $ticket) {
//                 $mode = strtolower($ticket->mode_of_payment ?? 'cash');
//                 $fee = (float)($ticket->payment->total_amount ?? 0);

//                 if ($mode === 'cash') $cashTotal += $fee;
//                 elseif ($mode === 'gcash') $gcashTotal += $fee;
//                 elseif ($mode === 'card') $cardTotal += $fee;
//             }

//             $logoBase64 = base64_encode(file_get_contents(public_path('images/comlogo.png')));

//             $data = [
//                 'tickets' => $tickets,
//                 'groupedTickets' => $groupedTickets,
//                 'totalParkFee' => $tickets->sum(fn($t) => $t->payment->total_amount ?? 0),
//                 'totalTickets' => $tickets->count(),
//                 'reportDate' => $reportDate,
//                 'company' => $company,
//                 'cashTotal' => $cashTotal,
//                 'gcashTotal' => $gcashTotal,
//                 'cardTotal' => $cardTotal,
//                 'logoBase64' => $logoBase64,
//                 'preparedBy' => $user ? $user->name : 'N/A',
//             ];

//             return Pdf::loadView('Printables.ParkingReport', $data)
//                 ->setPaper('a4', 'landscape')
//                 ->setOption('margin-top', 10)
//                 ->setOption('margin-right', 10)
//                 ->setOption('margin-bottom', 10)
//                 ->setOption('margin-left', 10)
//                 ->stream('parkout-report.pdf');
//         }
//  }
    



class ReportController extends Controller
{
    public function previewPDF(Request $request)
    {
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');

        $company = Company::first();

        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();

        $query = Ticket::whereBetween('tickets.park_out_datetime', [$from, $to]) 
            ->where('tickets.is_park_out', 1)
            ->where('tickets.remarks', 'Paid')
            ->whereNull('tickets.deleted_at')
            ->leftJoin('payments', 'payments.ticket_id', '=', 'tickets.id')
            ->select('tickets.*', 'payments.total_amount', 'payments.id as payment_id');

        if ($user = Auth::user()) {
            if ($user->role === 2) {
                $query->where('park_out_by', $user->id);
            } elseif ($user->role != 2 && $request->filled('staff') && $request->input('staff') != 'All') {
                $query->where('park_out_by', (int) $request->input('staff'));
            }
        }

        $tickets = $query->with(['payment.details'])->orderBy('park_out_datetime', 'desc')->get();

        // Add card_numbers attribute to each ticket
        $tickets->each(function ($ticket) {
            if ($ticket->payment && $ticket->payment->details->isNotEmpty()) {
                $cardNumbers = $ticket->payment->details->pluck('card_number')->filter()->toArray();
                $ticket->card_numbers = !empty($cardNumbers) ? implode('/', $cardNumbers) : 'N/A';
            } else {
                $ticket->card_numbers = 'N/A';
            }
        });

        // Group tickets by park_out_by (staff member)
        $groupedTickets = $tickets->groupBy('park_out_by')->map(function ($group) {
            $staffId = $group->first()->park_out_by;
            $staff = User::find($staffId);
            
            return [
                'staff_id' => $staffId,
                'staff_name' => $staff ? $staff->name : 'Unknown',
                'tickets' => $group,
                'total' => $group->sum(fn($t) => $t->payment->total_amount ?? 0),
                'count' => $group->count(),
            ];
        });

        $reportDate = $from->isSameDay($to)
            ? $from->format('F d, Y')
            : $from->format('F d, Y') . ' - ' . $to->format('F d, Y');

        $cashTotal = 0;
        $gcashTotal = 0;
        $cardTotal = 0;

        foreach ($tickets as $ticket) {
            $mode = strtolower($ticket->mode_of_payment ?? 'cash');
            $fee = (float)($ticket->payment->total_amount ?? 0);

            if ($mode === 'cash') $cashTotal += $fee;
            elseif ($mode === 'gcash') $gcashTotal += $fee;
            elseif ($mode === 'card') $cardTotal += $fee;
        }

        $logoBase64 = base64_encode(file_get_contents(public_path('images/comlogo.png')));

        $data = [
            'tickets' => $tickets,
            'groupedTickets' => $groupedTickets,
            'totalParkFee' => $tickets->sum(fn($t) => $t->payment->total_amount ?? 0),
            'totalTickets' => $tickets->count(),
            'reportDate' => $reportDate,
            'company' => $company,
            'cashTotal' => $cashTotal,
            'gcashTotal' => $gcashTotal,
            'cardTotal' => $cardTotal,
            'logoBase64' => $logoBase64,
            'preparedBy' => $user ? $user->name : 'N/A',
        ];

        return Pdf::loadView('Printables.ParkingReport', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->stream('parkout-report.pdf');
    }
}

