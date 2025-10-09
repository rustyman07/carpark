<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Inertia\Inertia;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class ReportController extends Controller
{


    public function todayParkout()
    {
        $today = Carbon::today();
        
        $tickets = Ticket::whereDate('park_out_datetime', $today)
            ->where('is_park_out', 1)
            ->whereNull('deleted_at')
            ->orderBy('park_out_datetime', 'desc')
            ->get();
        
        $totalParkFee = $tickets->sum('park_fee');
        $totalTickets = $tickets->count();
        
        return Inertia::render('Reports/TodayParkout', [
            'tickets' => $tickets,
            'totalParkFee' => $totalParkFee,
            'totalTickets' => $totalTickets,
            'reportDate' => $today->format('F d, Y')
        ]);
    }




public function downloadPDF()
{
    $today = Carbon::today();

    $company = \App\Models\Company::first();
    
    $tickets = Ticket::whereDate('park_out_datetime', $today)
        ->where('is_park_out', 1)
        ->where('remarks','Paid')
        ->whereNull('deleted_at')
        ->orderBy('park_out_datetime', 'desc')
        ->get();
    
    $totalParkFee = $tickets->sum('park_fee');
    $totalTickets = $tickets->count();
    $reportDate = $today->format('F d, Y');
    
    $html = $this->generateReportHTML($tickets, $totalParkFee, $totalTickets, $reportDate,$company);
    
    $filename = 'parkout-report-' . $today->format('Y-m-d') . '.pdf';
    
    $pdf = Pdf::loadHTML($html)
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', 10)
        ->setOption('margin-right', 10)
        ->setOption('margin-bottom', 10)
        ->setOption('margin-left', 10);
    
     return $pdf->stream('parkout-report.pdf');
}

// Keep the same generateReportHTML method


private function generateReportHTML($tickets, $totalParkFee, $totalTickets, $reportDate,$company)
{
    $rows = '';
    foreach ($tickets as $index => $ticket) {
        $bgClass = $index % 2 === 1 ? 'background-color: #f9fafb;' : '';

        $parkIn = $ticket->park_datetime
            ? \Carbon\Carbon::parse($ticket->park_datetime)->format('M d, Y h:i A')
            : 'N/A';

        $parkOut = $ticket->park_out_datetime
            ? \Carbon\Carbon::parse($ticket->park_out_datetime)->format('M d, Y h:i A')
            : 'N/A';

        // --- Duration formatting using total_minutes ---
        $totalMinutes = $ticket->total_minutes ?? 0;
        $days = intdiv($totalMinutes, 1440);
        $hours = intdiv($totalMinutes % 1440, 60);
        $minutes = $totalMinutes % 60;

        $durationParts = [];
        if ($days > 0) $durationParts[] = "{$days}d";
        if ($hours > 0) $durationParts[] = "{$hours}h";
        if ($minutes > 0 || empty($durationParts)) $durationParts[] = "{$minutes}m";
        $duration = implode(' ', $durationParts);

        $rows .= "<tr style='border-bottom: 1px solid #e5e7eb; {$bgClass}'>
            <td style='padding: 12px 16px; font-size: 14px; font-weight: 500;'>{$ticket->ticket_no}</td>
            <td style='padding: 12px 16px; font-size: 14px;'>" . ($ticket->plate_no ?? 'N/A') . "</td>
            <td style='padding: 12px 16px; font-size: 14px;'>{$parkIn}</td>
            <td style='padding: 12px 16px; font-size: 14px;'>{$parkOut}</td>
            <td style='padding: 12px 16px; font-size: 14px;'>{$duration}</td>
            <td style='padding: 12px 16px; font-size: 14px;'>" . ($ticket->mode_of_payment ?? 'Cash') . "</td>
            <td style='padding: 12px 16px; font-size: 14px; text-align: right; font-weight: 500;'>" . number_format($ticket->park_fee, 2) . "</td>
        </tr>";
    }

    // ✅ Encode your logo as base64 to embed directly
    $logoBase64 = base64_encode(file_get_contents(public_path('images/comlogo.png')));

    return "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; }
            
            .header {
                border-bottom: 2px solid #1f2937;
                padding: 32px 48px;
                text-align: center;
                position: relative;
            }

            .header img {
                position: absolute;
                top: 0;
                left: 200px;
                height: 170px;
                width: 170px;
            }

            .header h1 { font-size: 24px; font-weight: bold; text-transform: uppercase; }
            .header h2 { font-size: 20px; font-weight: 600; margin-top: 8px; }
            .header p { font-size: 14px; color: #4b5563; margin-top: 12px; }

            .summary { padding: 24px 48px; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; }
            .summary h3 { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 16px; }
            .summary-grid { display: flex; gap: 24px; }
            .summary-item { flex: 1; border-left: 4px solid #2563eb; padding-left: 16px; }
            .summary-item.green { border-left-color: #16a34a; }
            .summary-label { font-size: 11px; color: #4b5563; text-transform: uppercase; }
            .summary-value { font-size: 24px; font-weight: bold; margin-top: 4px; }

            .content { padding: 24px 48px; }
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #f9fafb; padding: 12px 16px; text-align: left; font-size: 11px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #d1d5db; }
            tfoot tr { background-color: #f3f4f6; border-top: 2px solid #1f2937; }
            tfoot td { padding: 16px; font-weight: bold; font-size: 16px; }
            .text-right { text-align: right; }

            .footer { padding: 24px 48px; border-top: 1px solid #e5e7eb; }
            .signature-grid { display: flex; gap: 32px; margin-top: 32px; }
            .signature-item { flex: 1; text-align: center; }
            .signature-line { border-top: 2px solid #1f2937; padding-top: 8px; margin-top: 48px; }
            .signature-label { font-size: 14px; font-weight: 600; }
            .signature-role { font-size: 11px; color: #6b7280; margin-top: 4px; }
            .footer-note { text-align: center; font-size: 11px; color: #6b7280; margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
        </style>
    </head>
    <body>
        <div class='header'>
            <img src='data:image/png;base64,{$logoBase64}' alt='Company Logo' />
            <h1>{$company->name}</h1>
            <p>{$company->address}</p>
            <p>  {$company->contact}</p>
            <h2>Daily Parkout Report</h2>
            <p>Report Date: <strong>{$reportDate}</strong></p>
            <p style='font-size: 11px;'>Generated: " . now()->format('F d, Y h:i A') . "</p>
        </div>

        <div class='summary'>
            <h3>Summary</h3>
            <div class='summary-grid'>
                <div class='summary-item'>
                    <p class='summary-label'>Total Parkout Tickets</p>
                    <p class='summary-value'>{$totalTickets}</p>
                </div>
                <div class='summary-item green'>
                    <p class='summary-label'>Total Collection</p>
                    <p class='summary-value'>" . number_format($totalParkFee, 2) . "</p>
                </div>
            </div>
        </div>

        <div class='content'>
            <table>
                <thead>
                    <tr>
                        <th>Ticket No.</th>
                        <th>Plate No.</th>
                        <th>Park In</th>
                        <th>Park Out</th>
                        <th>Duration</th>
                        <th>Payment</th>
                        <th class='text-right'>Amount</th>
                    </tr>
                </thead>
                <tbody>{$rows}</tbody>
                <tfoot>
                    <tr>
                        <td colspan='6' class='text-right'>TOTAL AMOUNT:</td>
                        <td class='text-right'>" . number_format($totalParkFee, 2) . "</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class='footer'>
            <div class='signature-grid'>
                <div class='signature-item'>
                    <div class='signature-line'>
                        <p class='signature-label'>Prepared By</p>
                        <p class='signature-role'>Cashier / Staff</p>
                    </div>
                </div>
    
            </div>
            <div class='footer-note'>
                <p>This is a system-generated report</p>
            </div>
        </div>
    </body>
    </html>";
}



    
}