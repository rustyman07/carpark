<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }

        body {
          font-family: 'Roboto', sans-serif;
            font-size: 16pt;
            margin: 0;
            padding: 5mm;
            width: 70mm;
            line-height: 1.5;
        }

        .peso {
            font-family: 'DejaVu Sans', 'DejaVu Sans Mono', 'Courier New', monospace;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-name { font-size: 16pt; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .row { 
            display: flex; 
            justify-content: space-between; 
            margin: 5px 0;
            font-size: 13pt;
        }
        .barcode { text-align: center; margin: 12px 0; }
        .barcode img { max-width: 100%; }
        .terms { font-size: 11pt; margin-top: 12px; line-height: 1.5; }

        /* Logo styling */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
              .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo {
    
            width: 50mm;
            height: auto;
        }

        .section-label {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 8px;
        }

        .section-value {
            font-size: 13pt;
            margin-top: 2px;
        }

        .receipt-header {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .total-row {
            font-size: 14pt;
            font-weight: bold;
            margin: 6px 0;
        }
    </style>
</head>
<body>
    {{-- Company Logo --}}
    <div class="logo-container">
        @if(file_exists($logoPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo">
        @endif
    </div>
    <div class="divider"></div>

    <div class="center receipt-header">PARKING RECEIPT</div>

    <div class="divider"></div>

    <div class="row">
        <span>Ticket No:</span>
        <span class="bold">{{ $ticket->ticket_no }}</span>
    </div>
    <div class="row">
        <span>Plate No:</span>
        <span class="bold">{{ $ticket->plate_no }}</span>
    </div>

    <div class="divider"></div>

    <div>
        <div class="section-label">ENTRY:</div>
        <div class="section-value">{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('m/d/Y h:i:s A') }}</div>
    </div>

    <div style="margin-top: 8px;">
        <div class="section-label">EXIT:</div>
        <div class="section-value">{{ \Carbon\Carbon::parse($ticket->park_out_datetime)->format('m/d/Y h:i:s A') }}</div>
    </div>
{{-- DURATION SECTION --}}
@php
    $totalMinutes = $ticket->total_minutes ?? 0;
    $days = intdiv($totalMinutes, 1440);
    $hours = intdiv($totalMinutes % 1440, 60);
    $minutes = $totalMinutes % 60;

    $durationParts = [];

    if ($days > 0) {
        $durationParts[] = "{$days} " . ($days === 1 ? 'day' : 'days');
    }
    if ($hours > 0) {
        $durationParts[] = "{$hours} " . ($hours === 1 ? 'hour' : 'hours');
    }
    if ($minutes > 0 || empty($durationParts)) {
        $durationParts[] = "{$minutes} " . ($minutes === 1 ? 'min' : 'mins');
    }

    $duration = implode(' ', $durationParts);
@endphp


    <div style="margin-top: 8px;">
        <div class="section-label">DURATION:</div>
        <div class="section-value">{{ $duration }}</div>
    </div>

    <div class="divider"></div>

    <div class="center section-label" style="font-size: 15pt;">PAYMENT DETAILS</div>

    <div class="row">
        <span>Parking Fee:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($ticket->park_fee, 2) }}</span>
    </div>

    @if($payment->amount > 0)
    <div class="row">
        <span>Amount Received:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($payment->amount, 2) }}</span>
    </div>
    @endif

    <div class="row total-row">
        <span>TOTAL:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($payment->total_amount, 2) }}</span>
    </div>

    <div class="row total-row">
        <span>CHANGE:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($payment->change, 2) }}</span>
    </div>

    <div class="divider"></div>

    <div class="barcode">
        <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
        <div style="font-size: 12pt; margin-top: 5px;">{{ $ticket->ticket_no }}</div>
    </div>

    <div class="divider"></div>

    <div class="center">
        <div class="bold" style="font-size: 15pt;">THANK YOU!</div>
        <div style="font-size: 13pt; margin-top: 3px;">Drive Safely</div>
    </div>
</body>
</html>