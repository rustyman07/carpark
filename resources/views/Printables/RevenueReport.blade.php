<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1f2937; }
        .header { border-bottom: 2px solid #1f2937; padding: 32px 48px; text-align: center; position: relative; }
        .header img { position: absolute; top: 0; left: 30px; height: 170px; width: 170px; }
        .header h1 { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .header h2 { font-size: 20px; font-weight: 600; margin-top: 5px; }
        .header p { font-size: 14px; color: #4b5563; margin-top: 5px; }

        .summary { 
            padding: 24px 48px; 
            background-color: #ffffff; 
            border-bottom: 1px solid #e5e7eb; 
        }
        .summary h3 { 
            font-size: 14px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-bottom: 16px;
            color: #1f2937;
        }
        .summary-grid { 
            width: 100%;
        }
        .summary-item { 
            padding: 0;
            border: none;
            background: transparent;
            vertical-align: top;
        }
        .summary-label { 
            font-size: 10px; 
            color: #6b7280; 
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .summary-value { 
            font-size: 16px; 
            font-weight: bold; 
            margin-top: 6px;
            color: #1f2937;
        }

        .content { padding: 24px 48px; }
        table { width: 100%; border-collapse: collapse; }
        th { 
            background-color: #f9fafb; 
            padding: 12px 16px; 
            text-align: left; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-bottom: 2px solid #d1d5db;
            color: #374151;
        }
        td { 
            padding: 12px 16px; 
            font-size: 13px;
            color: #1f2937;
        }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody tr:hover { background-color: #f9fafb; }
        tfoot tr { background-color: #f3f4f6; border-top: 2px solid #1f2937; }
        tfoot td { padding: 16px; font-weight: bold; font-size: 14px; }
        .text-right { text-align: right; }
        .font-weight-500 { font-weight: 500; }
        .detail-row { 
            background-color: #fafafa !important; 
            font-size: 11px;
            color: #6b7280;
        }
        .detail-row td { padding: 8px 16px; }

        .footer { padding: 48px; border-top: 1px solid #e5e7eb; text-align: left; }
        .signature-line { 
            border-top: 2px solid #1f2937; 
            padding-top: 8px; 
            margin-top: 48px; 
            width: 250px; 
        }
        .signature-label { font-size: 14px; font-weight: 600; }
        .signature-role { font-size: 11px; color: #6b7280; margin-top: 4px; }
    </style>
</head>
<body>
    <div class='header'>
        @if($logoBase64)
        <img src='data:image/png;base64,{{ $logoBase64 }}' alt='Company Logo' />
        @endif
        <h1>{{ $company->name }}</h1>
        <p>{{ $company->address }}</p>
        <p>{{ $company->contact }}</p>
        <h2>Payment Report</h2>
        <p>Date Range: <strong>{{ $dateRange }}</strong></p>
        <p>Generated: <strong>{{ $reportDate }}</strong></p>
    </div>

    <div class='summary'>
        <h3>Summary</h3>
        <table class='summary-grid' cellpadding='0' cellspacing='0'>
            <tr>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Total Payments</p>
                        <p class='summary-value'>{{ $totalPayments }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Total Collection</p>
                        <p class='summary-value'>{{ number_format($totalCollected, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Cash Total</p>
                        <p class='summary-value'>{{ number_format($cashTotal, 2) }}</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>GCash Total</p>
                        <p class='summary-value'>{{ number_format($gcashTotal, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Card Total</p>
                        <p class='summary-value'>{{ number_format($cardTotal, 2) }}</p>
                    </div>
                </td>
                <td width='33.33%'>
                    <div class='summary-item'>
                        <p class='summary-label'>Total Change</p>
                        <p class='summary-value'>{{ number_format($totalChange, 2) }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class='content'>
        <table>
            <thead>
                <tr>
                    <th width='8%'>Payment ID</th>
                    <th width='12%'>Ticket No.</th>
                    <th width='18%'>Payment Date</th>
                    <th width='12%'>Type</th>
                    <th width='10%'>Method</th>
                    <th width='13%' class='text-right'>Amount Due</th>
                    <th width='13%' class='text-right'>Paid</th>
                    <th width='14%' class='text-right'>Change</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    @php
                        $paidAt = $payment->paid_at 
                            ? $payment->paid_at->format('M d, Y h:i A') 
                            : 'N/A';
                    @endphp
                    <tr>
                        <td class='font-weight-500'>#{{ $payment->id }}</td>
                        <td>{{ $payment->ticket->ticket_no ?? 'N/A' }}</td>
                        <td>{{ $paidAt }}</td>
                        <td>{{ ucfirst($payment->payment_type ?? 'Regular') }}</td>
                        <td>{{ $payment->payment_method ?? 'Cash' }}</td>
                        <td class='text-right'>{{ number_format($payment->total_amount, 2) }}</td>
                        <td class='text-right font-weight-500'>{{ number_format($payment->amount, 2) }}</td>
                        <td class='text-right'>{{ number_format($payment->change, 2) }}</td>
                    </tr>
                    
                    @if($payment->details->count() > 0)
                        @foreach($payment->details as $detail)
                        <tr class='detail-row'>
                            <td colspan='3' style='padding-left: 40px;'>
                                <em>Card: {{ $detail->card_name }} ({{ $detail->card_number }})</em>
                            </td>
                            <td colspan='2' class='text-right'>
                                Days: {{ $detail->no_of_days }} | Discount: {{ number_format($detail->discount, 2) }}
                            </td>
                            <td class='text-right'>{{ number_format($detail->balance, 2) }}</td>
                            <td class='text-right font-weight-500'>{{ number_format($detail->amount, 2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan='6' class='text-right'>TOTAL COLLECTED:</td>
                    <td class='text-right'>{{ number_format($totalCollected, 2) }}</td>
                    <td class='text-right'>{{ number_format($totalChange, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
   
    <div class='footer'>
        <div><p>{{ $preparedBy }}</p></div>
        <div class='signature-line'>
            <p class='signature-label'>Prepared By</p>
            <p class='signature-role'>Cashier / Staff</p>
        </div>
    </div>
</body>
</html>