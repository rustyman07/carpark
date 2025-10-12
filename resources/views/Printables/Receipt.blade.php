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
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            margin: 0;
            padding: 5mm;
            width: 70mm;
        }

        .peso {
            font-family: 'DejaVu Sans', 'DejaVu Sans Mono', 'Courier New', monospace;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-name { font-size: 14pt; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .barcode { text-align: center; margin: 10px 0; }
        .barcode img { max-width: 100%; }
        .terms { font-size: 8pt; margin-top: 10px; }

        /* Logo styling */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
        .logo {
            margin-top: -20px;
            margin-left: 70px;
            width: 30mm;
            height: auto;
        }
    </style>
</head>
<body>
    {{-- Company Logo (upper-left corner) --}}
    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo">
    @endif

    <!-- <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div>{{ $company->address }}</div>
        <div>{{ $company->contact }}</div>
    </div> -->

    <div class="divider"></div>

    <div class="center bold">PARKING RECEIPT</div>

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
        <div class="bold">ENTRY:</div>
        <div>{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('m/d/Y h:i:s A') }}</div>
    </div>

    <div style="margin-top: 5px;">
        <div class="bold">EXIT:</div>
        <div>{{ \Carbon\Carbon::parse($ticket->park_out_datetime)->format('m/d/Y h:i:s A') }}</div>
    </div>

    {{-- ✅ DURATION SECTION ADDED HERE --}}
    @php
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

    <div style="margin-top: 5px;">
        <div class="bold">DURATION:</div>
        <div>{{ $duration }}</div>
    </div>
    {{-- ✅ END OF DURATION SECTION --}}

    <div class="divider"></div>

    <div class="center bold">PAYMENT DETAILS</div>

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

    <div class="row bold">
        <span>TOTAL:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($payment->total_amount, 2) }}</span>
    </div>

    <div class="row bold">
        <span>CHANGE:</span>
        <span><span class="peso">&#8369;</span>{{ number_format($payment->change, 2) }}</span>
    </div>

    <div class="divider"></div>

    <div class="barcode">
        <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
        <div>{{ $ticket->ticket_no }}</div>
    </div>

    <div class="divider"></div>

    <div class="center">
        <div class="bold">THANK YOU!</div>
        <div>Drive Safely</div>
    </div>

    <!-- <div class="terms">
        <div class="center bold">TERMS & CONDITIONS</div>
        <div>• Ticket must be presented upon exit</div>
        <div>• Lost ticket subject to maximum fee</div>
        <div>• Management not liable for damage</div>
    </div> -->
</body>
</html>
