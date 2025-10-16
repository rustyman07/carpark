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
            font-size: 13pt;
            margin: 0;
            padding: 5mm;
            width: 70mm;
            line-height: 1.4;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-name { font-size: 16pt; font-weight: bold; margin-top: 5px; }
        .company-info { font-size: 12pt; margin: 3px 0; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .row { 
            display: flex; 
            justify-content: space-between; 
            margin: 6px 0;
            font-size: 13pt;
        }
        .section-title { font-size: 14pt; font-weight: bold; margin: 10px 0 6px 0; }
        
        /* Logo styling */
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo {
            width: 50mm;
            height: auto;
            margin: 0 auto;
        }

        /* QR Code styling */
        .qr-container {
            text-align: center;
            margin: 12px 0;
            padding: 10px 0;
        }
        .qr-code {
            width: 45mm;
            height: 45mm;
            margin: 0 auto;
        }
        .qr-label {
            font-size: 11pt;
            margin-top: 6px;
            font-weight: bold;
        }

        /* Info boxes */
        .info-box {
            background: #f5f5f5;
            padding: 8px;
            margin: 8px 0;
            border-radius: 3px;
        }

        .ticket-header {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .ticket-number {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 3px;
        }

        .plate-number {
            font-size: 18pt;
            font-weight: bold;
        }

        .footer-note {
            font-size: 11pt;
            margin-top: 4px;
            line-height: 1.5;
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

    {{-- Ticket Header --}}
    <div class="center ticket-header">PARKING TICKET</div>

    <div class="divider"></div>

    {{-- Ticket Number --}}
    <div class="center">
        <div style="margin-bottom: 5px; font-size: 13pt;">Ticket No:</div>
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
        <div class="bold" style="font-size: 14pt; margin-bottom: 6px;">IMPORTANT REMINDERS</div>
        <div class="footer-note">• Keep this ticket safe</div>
        <div class="footer-note">• Present at exit terminal</div>
        <div class="footer-note">• Payment upon exit</div>
    </div>

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="center">
        <div class="bold" style="font-size: 15pt;">THANK YOU!</div>
        <div class="footer-note">Drive Safely</div>
    </div>
</body>
</html>