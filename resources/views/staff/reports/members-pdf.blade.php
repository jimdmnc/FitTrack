<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
            background-color: #f9fafc;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e1e5eb;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            margin: 0;
        }
        
        .date {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .summary-box {
            background-color: #2c3e50;
            color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .summary-label {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 32px;
            font-weight: 600;
        }
        
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 25px;
            overflow: hidden;
        }
        
        .members-title {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f5f7fa;
        }
        
        th {
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            color: #7f8c8d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e1e5eb;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e1e5eb;
            color: #2c3e50;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .member-id {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .member-name {
            font-weight: 600;
        }
        
        .member-email {
            color: #3498db;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Members Report</h1>
        <div class="date">{{ date('F d, Y') }}</div>
    </div>
    
    <div class="summary-box">
        <div class="summary-label">Total Members</div>
        <div class="summary-value">{{ number_format($totalMembers) }}</div>
    </div>
    
    <div class="table-container">
        <h2 class="members-title">Member Directory</h2>
        <table>
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Join Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                <tr>
                    <td class="member-id">{{ $member->id }}</td>
                    <td class="member-name">{{ $member->name }}</td>
                    <td class="member-email">{{ $member->email }}</td>
                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        Generated on {{ date('F d, Y') }} • Report ID: {{ uniqid() }}
    </div>
</body>
</html>