<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rental Receipt</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            padding: 5px;
        }

        hr {
            margin: 1rem 0;
        }

        .footer {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>

</head>

<body>
    <h2>Rental Receipt</h2>
    <p><strong>Receipt No:</strong> RNT-{{ $record->rental_id }}</p>
    <p><strong>Date Issued:</strong> {{ now()->format('M d, Y') }}</p>
    <hr>
    <p><strong>Customer:</strong> {{ $record->customer?->first_name }} {{ $record->customer?->last_name }}</p>
    <p><strong>Product:</strong> {{ $record->product->name }}</p>
    <p><strong>Pickup Date:</strong> {{ \Carbon\Carbon::parse($record->pickup_date)->format('M d, Y') }}</p>
    <p><strong>Return Date:</strong> {{ \Carbon\Carbon::parse($record->return_date)->format('M d, Y') }}</p>
    <hr>
    <table>
        <tr>
            <td>Rental Price</td>
            <td align="right">₱{{ number_format($record->rental_price, 2) }}</td>
        </tr>
        <tr>
            <td>Deposit</td>
            <td align="right">₱{{ number_format($record->deposit_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Total Paid</td>
            <td align="right">₱{{ number_format($record->total_paid, 2) }}</td>
        </tr>
        <tr>
            <td>Balance Due</td>
            <td align="right">₱{{ number_format($record->balance_due, 2) }}</td>
        </tr>
    </table>
    <hr>
    <p><strong>Status:</strong> {{ $record->rental_status }}</p>
    <p><strong>Handled By:</strong> {{ auth()->user()->name ?? 'System' }}</p>
    <div class="footer">
        Thank you for renting with us!
    </div>
</body>

</html>