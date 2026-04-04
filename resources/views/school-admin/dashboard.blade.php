<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Admin Dashboard — Scorz</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            min-height: 100vh;
        }
        /* Nav */
        .nav {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #f1f5f9;
        }
        .nav-brand-icon {
            width: 32px; height: 32px;
            background: #6366f1;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-brand-icon svg { color: #fff; }
        .role-pill {
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            color: #818cf8;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.65rem;
            border-radius: 20px;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .nav-user {
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .logout-btn {
            background: none;
            border: 1px solid #334155;
            color: #94a3b8;
            padding: 0.35rem 0.85rem;
            border-radius: 7px;
            font-size: 0.82rem;
            cursor: pointer;
            transition: border-color 0.15s, color 0.15s;
        }
        .logout-btn:hover { border-color: #6366f1; color: #818cf8; }
        /* Main */
        .main { padding: 2rem; max-width: 1100px; margin: 0 auto; }
        .page-header { margin-bottom: 2rem; }
        .page-header h2 { font-size: 1.4rem; font-weight: 700; color: #f1f5f9; }
        .page-header p  { color: #64748b; font-size: 0.9rem; margin-top: 0.2rem; }
        /* School cards */
        .section-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }
        .school-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .school-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 1.25rem;
        }
        .school-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .school-icon {
            width: 40px; height: 40px;
            background: rgba(99,102,241,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .school-icon svg { color: #818cf8; }
        .school-name { font-weight: 600; font-size: 0.95rem; color: #f1f5f9; }
        .school-meta { font-size: 0.78rem; color: #64748b; margin-top: 0.15rem; }
        .school-code {
            display: inline-block;
            background: #0f172a;
            border: 1px solid #334155;
            color: #94a3b8;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 5px;
            font-family: monospace;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 2.25
                             2.25 0 003 8.118v1.632c0 5.193 3.663 10.003 8.723 11.228A11.962
                             11.962 0 0021 9.75v-1.632a2.25 2.25 0 00-.598-1.518A11.959 11.959
                             0 0112 2.714z"/>
                </svg>
            </div>
            Scorz
            <span class="role-pill">School Admin</span>
        </div>
        <div class="nav-right">
            <span class="nav-user">{{ $user->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Sign out</button>
            </form>
        </div>
    </nav>

    <main class="main">
        <div class="page-header">
            <h2>Welcome, {{ $user->name }}</h2>
            <p>You are managing {{ $schools->count() }} {{ Str::plural('school', $schools->count()) }}</p>
        </div>

        <div class="section-title">Your Schools</div>

        @if ($schools->isEmpty())
            <p style="color:#64748b;font-size:0.9rem;">No schools assigned to your account.</p>
        @else
            <div class="school-grid">
                @foreach ($schools as $school)
                    <div class="school-card">
                        <div class="school-card-header">
                            <div class="school-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5
                                             3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125
                                             1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                                </svg>
                            </div>
                            <div>
                                <div class="school-name">{{ $school->name }}</div>
                                <div class="school-meta">{{ ucfirst(str_replace('_', ' ', $school->school_type)) }} &bull; {{ $school->region }}</div>
                            </div>
                        </div>
                        <span class="school-code">{{ $school->code }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
