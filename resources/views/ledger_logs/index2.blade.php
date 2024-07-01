<!DOCTYPE html>
<html>
<head>
    <title>Ledger Logs</title>
    <!-- Add your CSS here -->
</head>
<body>
    <h1>Ledger Logs</h1>
    <table style="width:100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Transaction Id</th>
                <th>Created/Updated By</th>
                <th>Action</th>
                <th>Description</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->cashbookLedger->transaction_id }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>{{ $log->action }}</td>
                <td>
                    @if (is_array($log->description))
                        <ul>
                            @foreach ($log->description as $field => $change)
                                <li>{{ $field }}: {{ $change['old'] }} -> {{ $change['new'] }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $log->description }}
                    @endif
                </td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</body>
</html>
