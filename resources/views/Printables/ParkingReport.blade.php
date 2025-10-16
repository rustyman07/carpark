<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .header { border-bottom: 2px solid #1f2937; padding: 32px 48px; text-align: center; position: relative; }
        .header img { position: absolute; top: 0; left: 30px; height: 170px; width: 170px; }
        .header h1 { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .header h2 { font-size: 20px; font-weight: 600; margin-top: 5px; }
        .header p { font-size: 14px; color: #4b5563; margin-top: 5px; }

        .summary { 
            padding: 24px 48px; 
            background-color: #f9fafb; 
            border-bottom: 1px solid #e5e7eb; 
        }
        .summary h3 { 
            font-size: 12px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-bottom: 16px; 
        }
        .summary-grid { 
            width: 100%; 
        }
        .summary-item { 
            border-left: 4px solid #2563eb; 
            padding: 12px 16px; 
            background: white; 
            border-radius: 6px;
            vertical-align: top;
        }
        .summary-item.green { border-left-color: #16a34a; }
        .summary-item.orange { border-left-color: #f59e0b; }
        .summary-label { font-size: 11px; color: #4b5563; text-transform: uppercase; }
        .summary-value { font-size: 20px; font-weight: bold; margin-top: 4px; }

        .content { padding: 24px 48px; }
        .staff-section { margin-bottom: 16px; page-break-inside: avoid; }
        .staff-header { 
    
            padding: 12px 16px; 
            border-radius: 4px 4px 0 0;
            margin-bottom: 0;
        }
        .staff-header h4 { font-size: 14px; font-weight: bold; margin: 0; }
        .staff-header p { font-size: 12px; margin: 4px 0 0 0; opacity: 0.9; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f9fafb; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #d1d5db; }
        td { padding: 8px 10px; font-size: 12px; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(odd) { background-color: #f9fafb; }
        tfoot tr { background-color: #f3f4f6; border-top: 2px solid #1f2937; }
        tfoot td { padding: 16px; font-weight: bold; font-size: 16px; }
        .text-right { text-align: right; }
        .font-weight-500 { font-weight: 500; }

        .footer { padding: 48px; border-top: 1px solid #e5e7eb; text-align: left; width: fit-content; }
        .signature-grid { display: flex; justify-content: flex-start; }
        .signature-item { text-align: left; }
        .signature-line { border-top: 2px solid #1f2937; padding-top: 8px; margin-top: 8px; width: 200px; }
        .signature-label { font-size: 14px; font-weight: 600; }
        .signature-role { font-size: 11px; color: #6b7280; margin-top: 4px; }
    </style>
</head>
<body>
    <div class='header'>
        <img src='data:image/png;base64,{{ $logoBase64 }}' alt='Company Logo' />
        <h1>{{ $company->name }}</h1>
        <p>{{ $company->address }}</p>
        <p>{{ $company->contact }}</p>
        <h2>Ticket Report</h2>
        <p>Date: <strong>{{ $reportDate }}</strong></p>
    </div>

    <div class='summary'>
        <h3>Summary</h3>
        <table class='summary-grid' cellpadding='10' cellspacing='0'>
            <tr>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Total Parkout Tickets</p>
                        <p class='summary-value'>{{ $totalTickets }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item green'>
                        <p class='summary-label'>GCash Total</p>
                        <p class='summary-value'>{{ number_format($gcashTotal, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item orange'>
                        <p class='summary-label'>Cash Total</p>
                        <p class='summary-value'>{{ number_format($cashTotal, 2) }}</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Total Collection</p>
                        <p class='summary-value'>{{ number_format($totalParkFee, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Card Total</p>
                        <p class='summary-value'>{{ number_format($cardTotal, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'></td>
            </tr>
        </table>
    </div>

    <div class='content'>
        @foreach($groupedTickets as $group)
            <div class='staff-section'>
                <div class='staff-header'>
                    <h4>Park out by: {{ $group['staff_name'] }}</h4>
                    <p>Tickets: {{ $group['count'] }} | Total: {{ number_format($group['total'], 2) }}</p>
                </div>
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
                    <tbody>
                        @foreach($group['tickets'] as $ticket)
                            @php
                                $parkIn = $ticket->park_datetime 
                                    ? \Carbon\Carbon::parse($ticket->park_datetime)->format('M d, Y h:i:s A') 
                                    : 'N/A';
                                
                                $parkOut = $ticket->park_out_datetime 
                                    ? \Carbon\Carbon::parse($ticket->park_out_datetime)->format('M d, Y h:i:s A') 
                                    : 'N/A';
                                
                                $totalMinutes = $ticket->total_minutes ?? 0;
                                $days = intdiv($totalMinutes, 1440);
                                $hours = intdiv($totalMinutes % 1440, 60);
                                $minutes = $totalMinutes % 60;
                                
                                $durationParts = [];
                                if ($days > 0) $durationParts[] = "{$days}d";
                                if ($hours > 0) $durationParts[] = "{$hours}h";
                                if ($minutes > 0 || empty($durationParts)) $durationParts[] = "{$minutes}m";
                                $duration = implode(' ', $durationParts);
                            @endphp
                            <tr>
                                <td class='font-weight-500'>{{ $ticket->ticket_no }}</td>
                                <td>{{ $ticket->plate_no ?? 'N/A' }}</td>
                                <td>{{ $parkIn }}</td>
                                <td>{{ $parkOut }}</td>
                                <td>{{ $duration }}</td>
                                <td>{{ $ticket->mode_of_payment ?? 'Cash' }}</td>
                                <td class='text-right font-weight-500'>{{ number_format($ticket->park_fee, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='6' class='text-right'>SUBTOTAL:</td>
                            <td class='text-right'>{{ number_format($group['total'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    </div>

    <div class='footer'>
        <div><p class=''>{{ $preparedBy }}</p></div>
        <div class='signature-grid'>
            <div class='signature-item'>
                <div class='signature-line'>
                    <p class='signature-label'>Prepared By</p>
                    <p class='signature-role'>Cashier / Staff</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>