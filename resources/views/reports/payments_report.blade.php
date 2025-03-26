<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Payments Report</h1>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Activation Date</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->user->first_name }} {{ $payment->user->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->activation_date)->format('m/d/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->expiry_date)->format('m/d/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
