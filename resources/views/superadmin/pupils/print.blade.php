<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pupil Report — {{ $school?->name ?? 'All Schools' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; background: #fff; }
        .page-header { padding: 20px 24px 12px; border-bottom: 2px solid #6366f1; display:flex; justify-content:space-between; align-items:flex-end; }
        .page-header h1 { font-size: 16px; font-weight: 700; color: #1e293b; }
        .page-header p  { font-size: 10px; color: #64748b; margin-top: 3px; }
        .meta { font-size: 10px; color: #64748b; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f1f5f9; padding: 7px 8px; text-align: left; font-size: 9px;
             text-transform: uppercase; letter-spacing: .05em; color: #64748b;
             border-bottom: 1px solid #e2e8f0; font-weight: 700; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 999px; font-size: 9px; font-weight: 600; }
        .badge-active   { background: #dcfce7; color: #16a34a; }
        .badge-inactive { background: #fee2e2; color: #dc2626; }
        .badge-sen      { background: #ede9fe; color: #7c3aed; }
        .pupil-id { font-family: monospace; color: #6366f1; font-weight: 700; }
        footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0;
                 font-size: 9px; color: #94a3b8; display: flex; justify-content: space-between; }
        @media print {
            @page { margin: 1.5cm; size: A4 landscape; }
            body  { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#1e293b;padding:10px 20px;display:flex;align-items:center;gap:12px">
    <button onclick="window.print()"
            style="background:#6366f1;color:#fff;border:none;padding:7px 18px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600">
        Print / Save as PDF
    </button>
    <a href="{{ url()->previous() }}"
       style="color:#94a3b8;font-size:12px;text-decoration:none">&larr; Back</a>
</div>

<div class="page-header">
    <div>
        <h1>Pupil Report — {{ $school?->name ?? 'All Schools' }}</h1>
        <p>Total pupils: {{ $pupils->count() }}</p>
    </div>
    <div class="meta">
        Generated: {{ now()->format('d M Y, H:i') }}<br>
        Scorz School Management
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Pupil ID</th>
            <th>Surname</th>
            <th>First Name</th>
            <th>Date of Birth</th>
            <th>Age</th>
            <th>School</th>
            <th>Year</th>
            <th>Class</th>
            <th>Teacher</th>
            <th>SEN</th>
            <th>In Averages</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pupils as $i => $pupil)
            <tr>
                <td style="color:#94a3b8">{{ $i + 1 }}</td>
                <td><span class="pupil-id">{{ $pupil->pupil_id }}</span></td>
                <td><strong>{{ $pupil->last_name }}</strong></td>
                <td>{{ $pupil->first_name }}</td>
                <td>{{ $pupil->date_of_birth?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $pupil->age ?? '—' }}</td>
                <td>{{ $pupil->school?->name ?? '—' }}</td>
                <td>{{ $pupil->year_group ?? '—' }}</td>
                <td>{{ $pupil->class_name ?? '—' }}</td>
                <td>{{ $pupil->teacher?->name ?? '—' }}</td>
                <td>
                    @if($pupil->sen !== 'none')
                        <span class="badge badge-sen">{{ $pupil->sen_label }}</span>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $pupil->include_in_averages ? 'Yes' : 'No' }}</td>
                <td>
                    <span class="badge {{ $pupil->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $pupil->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="13" style="text-align:center;padding:20px;color:#94a3b8">No pupils found.</td></tr>
        @endforelse
    </tbody>
</table>

<footer>
    <span>Scorz — Confidential</span>
    <span>{{ now()->format('d/m/Y') }}</span>
</footer>

</body>
</html>
