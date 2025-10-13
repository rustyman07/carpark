
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
            font-size: 9pt;
            margin: 0;
            padding: 3mm 5mm;
            width: 70mm;
            line-height: 1.3;
        }

        .peso {
            font-family: 'DejaVu Sans', 'DejaVu Sans Mono', 'Courier New', monospace;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .row { 
            display: flex; 
            justify-content: space-between; 
            margin: 2px 0;
            font-size: 9pt;
        }
        
        /* Logo styling */
        .logo {
            width: 20mm;
            height: auto;
            margin: 0 auto 3px;
            display: block;
        }

        /* QR Code styling */
        .qr-code {
            width: 20mm;
            height: 20mm;
            margin: 5px auto;
            display: block;
        }

        /* Card Number */
        .card-number {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 3px 0;
        }

        /* Amount */
        .amount {
            font-size: 16pt;
            font-weight: bold;
        }

        /* Days */
        .days-badge {
            display: inline-block;
            background: #333;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
        }

        /* Validity compact */
        .validity {
            background: #f5f5f5;
            padding: 4px;
            margin: 4px 0;
            font-size: 8pt;
        }

        .footer {
            font-size: 7pt;
            margin-top: 5px;
            color: #666;
        }
    </style>
</head>
<body>
    {{-- Logo --}}
    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo">
    @endif

    {{-- Card Number --}}
    <div class="center">
        <div style="font-size: 8pt; margin-bottom: 2px;">CARD NO.</div>
        <div class="card-number">{{ $card->card_number }}</div>
    </div>


        <!-- {{-- Company Information --}}
    <div class="center">
        <div class="company-name">{{ $company->name }}</div>
        <div class="company-info">{{ $company->address }}</div>
        <div class="company-info">{{ $company->contact }}</div>
    </div> -->

    <!-- <div class="divider"></div> -->

    <!-- {{-- Card Header --}}
    <div class="center card-header">PARKING CARD</div>
    <div class="center">
        <span class="status-active">ACTIVE</span>
    </div> 
    -->
    <div class="divider"></div>

    {{-- QR Code --}}
    <div class="center">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-code" alt="QR">
        <div style="font-size: 7pt; margin-top: 2px;">SCAN FOR ENTRY/EXIT</div>
    </div>

    <div class="divider"></div>

    {{-- Amount & Days --}}
    <div class="row">
        <span>Amount:</span>
        <span class="amount"><span class="peso">₱</span>{{ number_format($card->amount, 2) }}</span>
    </div>
    <div class="row">
        <span>Balance:</span>
        <span class="bold" style="font-size: 11pt;"><span class="peso">₱</span>{{ number_format($card->balance, 2) }}</span>
    </div>
    <div class="row">
        <span>Days:</span>
        <span class="days-badge">{{ $card->no_of_days }} {{ $card->no_of_days > 1 ? 'DAYS' : 'DAY' }}</span>
    </div>

    <div class="divider"></div>

    {{-- Validity --}}
    @if($card->valid_until)
    <div class="validity">
        <div class="row">
            <span>Valid From:</span>
            <span class="bold">{{ \Carbon\Carbon::parse($card->created_at)->format('M d, Y') }}</span>
        </div>
        <div class="row">
            <span>Valid Until:</span>
            <span class="bold">{{ \Carbon\Carbon::parse($card->valid_until)->format('M d, Y ') }}</span>
        </div>
    </div>
     @endif

    <div class="divider"></div>

    {{-- Instructions - Compact --}}
    <div class="center" style="font-size: 7pt; line-height: 1.4;">
        <div class="bold" style="font-size: 8pt; margin-bottom: 2px;">REMINDERS</div>
        Keep card safe • Scan at entry/exit • Non-refundable
    </div>

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="center">
        <div class="bold" style="font-size: 9pt;">THANK YOU!</div>
    </div>

    <div class="footer center">
        {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
