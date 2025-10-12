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

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-name { font-size: 14pt; font-weight: bold; margin-top: 5px; }
        .company-info { font-size: 9pt; margin: 2px 0; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin: 4px 0; }
        .section-title { font-size: 11pt; font-weight: bold; margin: 8px 0 4px 0; }
        
        /* Logo styling */
        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo {
            width: 25mm;
            height: auto;
            margin: 0 auto;
        }

        /* QR Code styling */
        .qr-container {
            text-align: center;
            margin: 10px 0;
            padding: 8px 0;
        }
        .qr-code {
            width: 40mm;
            height: 40mm;
            margin: 0 auto;
        }
        .qr-label {
            font-size: 8pt;
            margin-top: 5px;
        }

        /* Info boxes */
        .info-box {
            background: #f5f5f5;
            padding: 5px;
            margin: 5px 0;
            border-radius: 3px;
        }

        .ticket-header {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .ticket-number {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .plate-number {
            font-size: 14pt;
            font-weight: bold;
        }

        .footer-note {
            font-size: 8pt;
            margin-top: 8px;
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

    <!-- {{-- Company Information --}}
    <div class="center">
        <div class="company-name">{{ $company->name }}</div>
        <div class="company-info">{{ $company->address }}</div>
        <div class="company-info">{{ $company->contact }}</div>
    </div> -->

    <div class="divider"></div>

    {{-- Ticket Header --}}
    <div class="center ticket-header">PARKING TICKET</div>

    <div class="divider"></div>

    {{-- Ticket Number --}}
    <div class="center">
        <div style="margin-bottom: 3px;">Ticket No:</div>
        <div class="ticket-number">{{ $ticket->ticket_no }}</div>
    </div>

    <div class="divider"></div>

    {{-- Vehicle Information --}}
    <div class="info-box">
        <div class="row">
            <span>Plate Number:</span>
            <span class="plate-number">{{ $ticket->plate_no }}</span>
        </div>
    </div>

    {{-- Entry Date & Time --}}
    <div class="section-title">ENTRY DETAILS</div>
    <div class="row">
        <span>Date:</span>
        <span class="bold">{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('M d, Y') }}</span>
    </div>
    <div class="row">
        <span>Time:</span>
        <span class="bold">{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('h:i:s A') }}</span>
    </div>

    <div class="divider"></div>

    {{-- QR Code Section --}}
    <div class="qr-container">
        <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
        <div class="qr-label">Scan QR Code at Exit</div>
    </div>

    <div class="divider"></div>

    {{-- Instructions --}}
    <div class="center">
        <div class="bold">IMPORTANT REMINDERS</div>
        <div class="footer-note">• Keep this ticket safe</div>
        <div class="footer-note">• Present at exit terminal</div>
        <div class="footer-note">• Payment upon exit</div>
    </div>

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="center">
        <div class="bold">THANK YOU!</div>
        <div class="footer-note">Drive Safely</div>
    </div>

    <!-- <div style="margin-top: 10px;" class="center footer-note">
        {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}
    </div> -->
</body>
</html>


<!-- <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: 80mm 100mm;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            margin: 0;
            padding: 3mm 5mm;
            width: 70mm;
            line-height: 1.2;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 4px 0; }
        .row { 
            display: flex; 
            justify-content: space-between; 
            margin: 2px 0;
            font-size: 9pt;
        }
        
        /* Logo styling */
        .logo {
            width: 18mm;
            height: auto;
            margin: 0 auto 2px;
            display: block;
        }

        /* Ticket Header */
        .ticket-header {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }

        /* Ticket Number */
        .ticket-number {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 2px 0;
        }

        /* Plate Number */
        .plate-number {
            font-size: 12pt;
            font-weight: bold;
            background: #f0f0f0;
            padding: 3px 8px;
            display: inline-block;
            border-radius: 2px;
        }

        /* QR Code styling */
        .qr-code {
            width: 28mm;
            height: 28mm;
            margin: 4px auto;
            display: block;
        }

        .footer {
            font-size: 7pt;
            margin-top: 3px;
            color: #666;
        }
    </style>
</head>
<body>
    {{-- Logo --}}
    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo">
    @endif

    {{-- Ticket Header --}}
    <div class="center ticket-header">PARKING TICKET</div>

    <div class="divider"></div>

    {{-- Ticket Number --}}
    <div class="center">
        <div style="font-size: 8pt; margin-bottom: 1px;">Ticket No.</div>
        <div class="ticket-number">{{ $ticket->ticket_no }}</div>
    </div>

    <div class="divider"></div>

    {{-- Plate Number --}}
    <div class="center">
        <div style="font-size: 8pt; margin-bottom: 2px;">Plate Number</div>
        <div class="plate-number">{{ $ticket->plate_no }}</div>
    </div>

    <div class="divider"></div>

    {{-- Entry Details - Compact --}}
    <div class="row">
        <span>Date:</span>
        <span class="bold">{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('M d, Y') }}</span>
    </div>
    <div class="row">
        <span>Time:</span>
        <span class="bold">{{ \Carbon\Carbon::parse($ticket->park_datetime)->format('h:i A') }}</span>
    </div>

    <div class="divider"></div>

    {{-- QR Code --}}
    <div class="center">
        <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
        <div style="font-size: 7pt; margin-top: 2px;">SCAN AT EXIT</div>
    </div>

    <div class="divider"></div>

    {{-- Reminders - Compact --}}
    <div class="center" style="font-size: 7pt; line-height: 1.3;">
        <div class="bold" style="font-size: 8pt; margin-bottom: 2px;">REMINDERS</div>
        Keep ticket safe • Present at exit • Pay on exit
    </div>

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="center">
        <div class="bold" style="font-size: 9pt;">THANK YOU!</div>
        <div style="font-size: 7pt;">Drive Safely</div>
    </div>
</body>
</html> -->




