<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .wrap { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #6366f1; padding: 28px 32px; }
        .header h1 { color: #fff; font-size: 1.2rem; }
        .header p  { color: #c7d2fe; margin-top: 4px; font-size: .85rem; }
        .body { padding: 28px 32px; }
        .body p { color: #374151; line-height: 1.6; font-size: .9rem; margin-bottom: 14px; }
        .creds { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 22px; margin: 20px 0; }
        .creds table { width: 100%; border-collapse: collapse; }
        .creds td { padding: 7px 0; font-size: .875rem; color: #374151; vertical-align: top; }
        .creds td:first-child { color: #6b7280; width: 160px; font-weight: 500; }
        .creds td strong { color: #111827; font-family: monospace; font-size: .95rem; }
        .pin-box { display: inline-block; background: #1e1b4b; color: #a5b4fc;
                   font-family: monospace; font-size: 1.4rem; letter-spacing: .4rem;
                   padding: 10px 20px; border-radius: 6px; font-weight: 700; margin: 4px 0; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb;
                  font-size: .78rem; color: #9ca3af; }
        .warning { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px;
                   padding: 10px 14px; font-size: .8rem; color: #92400e; margin-top: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>New Pupil Account Created</h1>
        <p>{{ $pupil->school->name ?? 'Scorz' }}</p>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $recipient->name }}</strong>,</p>
        <p>A new pupil account has been created. Please share the credentials below with the pupil.</p>

        <div class="creds">
            <table>
                <tr>
                    <td>Pupil Name</td>
                    <td><strong>{{ $pupil->full_name }}</strong></td>
                </tr>
                <tr>
                    <td>Pupil ID</td>
                    <td><strong>{{ $pupil->pupil_id }}</strong></td>
                </tr>
                <tr>
                    <td>School</td>
                    <td><strong>{{ $pupil->school->name ?? '—' }}</strong></td>
                </tr>
                <tr>
                    <td>Year / Class</td>
                    <td><strong>{{ $pupil->year_group }} — {{ $pupil->class_name }}</strong></td>
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td><strong>{{ $pupil->date_of_birth?->format('d M Y') ?? '—' }}</strong></td>
                </tr>
                <tr>
                    <td>Login PIN</td>
                    <td>
                        <div class="pin-box">{{ $plainPin }}</div>
                    </td>
                </tr>
                <tr>
                    <td>Login URL</td>
                    <td><strong>{{ url('/pupil/login') }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="warning">
            ⚠ The pupil logs in using their <strong>Pupil ID</strong> and the <strong>6-digit PIN</strong> above.
            Please keep this information secure.
        </div>
    </div>
    <div class="footer">This email was sent automatically by Scorz. Do not reply.</div>
</div>
</body>
</html>
