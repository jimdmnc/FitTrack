<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Report</title>
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

    <h1>Members Report</h1>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Membership</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</td>
                    <td>{{ $attendance->user->membership_type_name }}</td>
                    <td>{{ $attendance->time_in ? $attendance->time_in->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $attendance->time_in ? $attendance->time_in->format('h:i A') : 'N/A' }}</td>
                    <td>{{ $attendance->time_out ? $attendance->time_out->format('h:i A') : 'N/A' }}</td>
                    <td>{{ $attendance->user->phone_number ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
