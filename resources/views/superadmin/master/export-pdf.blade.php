<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['label'] }} — Master Data Export</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            padding: 2rem;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #6366f1;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header h1 { font-size: 1.4rem; font-weight: 800; color: #1e293b; }
        .header .meta { font-size: .78rem; color: #64748b; text-align: right; }
        .header .badge-system {
            font-size: .7rem; font-weight: 700; background: #6366f1; color: #fff;
            padding: .2rem .6rem; border-radius: 4px; display: inline-block; margin-bottom: .3rem;
        }

        /* Stats bar */
        .stats {
            display: flex; gap: 1.5rem; margin-bottom: 1.5rem;
            padding: .75rem 1rem; background: #f8fafc; border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .stat { }
        .stat-value { font-size: 1.2rem; font-weight: 800; color: #1e293b; }
        .stat-label { font-size: .72rem; color: #64748b; margin-top: .1rem; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f1f5f9; }
        th {
            text-align: left; padding: .55rem .75rem;
            font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: .55rem .75rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbff; }

        /* Badges */
        .badge {
            display: inline-block; padding: .15rem .5rem;
            border-radius: 9999px; font-size: .68rem; font-weight: 700;
        }
        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        /* Footer */
        .footer {
            margin-top: 2rem; padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between;
            font-size: .72rem; color: #94a3b8;
        }

        /* Print actions (hidden when printing) */
        .print-actions {
            margin-bottom: 1.5rem;
            display: flex; gap: .75rem;
        }
        .btn-print {
            background: #6366f1; color: #fff; border: none;
            padding: .5rem 1.25rem; border-radius: 7px; font-size: .85rem;
            font-weight: 600; cursor: pointer;
        }
        .btn-close {
            background: none; border: 1px solid #cbd5e1; color: #475569;
            padding: .5rem 1rem; border-radius: 7px; font-size: .85rem;
            cursor: pointer;
        }

        @media print {
            .print-actions { display: none; }
            body { padding: 1rem; }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button class="btn-print" onclick="window.print()">
        🖨 Print / Save as PDF
    </button>
    <button class="btn-close" onclick="window.close()">Close</button>
</div>

<div class="header">
    <div>
        <div class="badge-system">Scorz — Master Data</div>
        <h1>{{ $config['label'] }}</h1>
    </div>
    <div class="meta">
        <div>Exported: {{ now()->format('d M Y, H:i') }}</div>
        <div>Total entries: {{ $entries->count() }}</div>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat-value">{{ $entries->count() }}</div>
        <div class="stat-label">Total</div>
    </div>
    <div class="stat">
        <div class="stat-value">{{ $entries->where('is_active', true)->count() }}</div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat">
        <div class="stat-value">{{ $entries->where('is_active', false)->count() }}</div>
        <div class="stat-label">Inactive</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:50px">#</th>
            <th>Name</th>
            <th style="width:100px">Status</th>
            <th style="width:120px">Date Added</th>
        </tr>
    </thead>
    <tbody>
        @forelse($entries as $i => $entry)
            <tr>
                <td style="color:#94a3b8">{{ $i + 1 }}</td>
                <td style="font-weight:600">{{ $entry->name }}</td>
                <td>
                    <span class="badge {{ $entry->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $entry->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $entry->created_at->format('d M Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:2rem;color:#94a3b8">
                    No entries found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>Scorz Admin — {{ $config['label'] }} Master Data</span>
    <span>Generated {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
