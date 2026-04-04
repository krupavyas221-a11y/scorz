<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .wrap { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #6366f1; padding: 28px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 1.2rem; }
        .header p  { color: #c7d2fe; margin: 4px 0 0; font-size: .85rem; }
        .body { padding: 28px 32px; }
        .body p { color: #374151; line-height: 1.6; margin: 0 0 14px; font-size: .9rem; }
        .creds { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 22px; margin: 20px 0; }
        .creds table { width: 100%; border-collapse: collapse; }
        .creds td { padding: 6px 0; font-size: .875rem; color: #374151; }
        .creds td:first-child { color: #6b7280; width: 150px; font-weight: 500; }
        .creds td strong { color: #111827; font-family: monospace; font-size: .95rem; }
        .btn { display: inline-block; background: #6366f1; color: #fff; text-decoration: none;
               padding: 10px 24px; border-radius: 6px; font-size: .875rem; font-weight: 600; margin: 8px 0 20px; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb;
                  font-size: .78rem; color: #9ca3af; }
        .warning { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px;
                   padding: 10px 14px; font-size: .8rem; color: #92400e; margin-top: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Welcome to Scorz</h1>
        <p>Teacher Account Created — {{ $school->name }}</p>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your teacher account has been created for <strong>{{ $school->name }}</strong>. Use the credentials below to log in.</p>

        <div class="creds">
            <table>
                <tr><td>Login URL</td>   <td><strong>{{ url('/login') }}</strong></td></tr>
                <tr><td>Email</td>        <td><strong>{{ $user->email }}</strong></td></tr>
                <tr><td>Password</td>     <td><strong>{{ $plainPassword }}</strong></td></tr>
                <tr><td>PIN</td>          <td><strong>{{ $plainPin }}</strong></td></tr>
                <tr><td>School</td>       <td><strong>{{ $school->name }}</strong></td></tr>
                @if($user->teacherAssignments->isNotEmpty())
                <tr>
                    <td>Assignment</td>
                    <td>
                        <strong>
                            {{ $user->teacherAssignments->map(fn($a) => $a->school_year.' — '.$a->class_name)->join(', ') }}
                        </strong>
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <a href="{{ url('/login') }}" class="btn">Log In Now</a>

        <div class="warning">
            ⚠ Please change your password and PIN after your first login.
        </div>
    </div>
    <div class="footer">This email was sent automatically by Scorz. Do not reply.</div>
</div>
</body>
</html>
