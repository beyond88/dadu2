<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warehouse List</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 9px; color: #2d2d2d; background: #fff; }

        .header { border-bottom: 2px solid #3b7ddd; padding-bottom: 8px; margin-bottom: 12px; }
        .header .title { font-size: 16px; font-weight: bold; color: #3b7ddd; }
        .header .meta { font-size: 8px; color: #666; margin-top: 3px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background-color: #3b7ddd; color: #fff; }
        thead th { padding: 5px 6px; text-align: left; font-size: 8.5px; font-weight: bold; border: 1px solid #2a6abf; white-space: nowrap; }
        tbody tr:nth-child(even) { background-color: #f4f7fb; }
        tbody tr:nth-child(odd)  { background-color: #ffffff; }
        tbody td { padding: 4px 6px; border: 1px solid #dde3ec; font-size: 8.5px; vertical-align: middle; }

        .badge-active   { color: #1a7a40; font-weight: bold; }
        .badge-inactive { color: #b33a3a; font-weight: bold; }
        .badge-default  { font-size: 7.5px; color: #3b7ddd; }

        .footer { margin-top: 12px; border-top: 1px solid #dde3ec; padding-top: 5px; font-size: 7.5px; color: #999; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Warehouse List</div>
        <div class="meta">
            Generated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
            @if($startDate || $endDate)
                &nbsp;&nbsp;|&nbsp;&nbsp;Date Range:
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : '—' }}
                &nbsp;to&nbsp;
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '—' }}
            @endif
            &nbsp;&nbsp;|&nbsp;&nbsp;Total: {{ $warehouses->count() }} warehouse(s)
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px;">#</th>
                <th style="width:90px;">Warehouse Name</th>
                <th style="width:110px;">Email</th>
                <th style="width:75px;">Phone</th>
                <th style="width:85px;">Company Name</th>
                <th style="width:90px;">Address 1</th>
                <th style="width:90px;">Address 2</th>
                <th style="width:45px;">Status</th>
                <th style="width:60px;">Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouses as $warehouse)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{ $warehouse->name }}
                    @if($warehouse->is_default)
                        <br><span class="badge-default">(Default)</span>
                    @endif
                </td>
                <td>{{ $warehouse->email ?? '—' }}</td>
                <td>{{ $warehouse->phone ?? '—' }}</td>
                <td>{{ $warehouse->company_name ?? '—' }}</td>
                <td>{{ $warehouse->address_1 ?? '—' }}</td>
                <td>{{ $warehouse->address_2 ?? '—' }}</td>
                <td class="{{ $warehouse->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                    {{ strtoupper($warehouse->status) }}
                </td>
                <td>{{ $warehouse->created_at ? $warehouse->created_at->format('d M Y') : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:10px; color:#999;">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        ClanVent &mdash; Warehouse Report &mdash; {{ \Carbon\Carbon::now()->format('Y') }}
    </div>

</body>
</html>
