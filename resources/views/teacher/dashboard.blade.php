<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard — Scorz</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            min-height: 100vh;
        }
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
            display: flex; align-items: center; gap: 0.6rem;
            font-weight: 700; font-size: 1.1rem; color: #f1f5f9;
        }
        .nav-brand-icon {
            width: 32px; height: 32px; background: #0891b2;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-brand-icon svg { color: #fff; }
        .role-pill {
            background: rgba(8,145,178,0.15);
            border: 1px solid rgba(8,145,178,0.3);
            color: #22d3ee;
            font-size: 0.75rem; font-weight: 600;
            padding: 0.2rem 0.65rem; border-radius: 20px;
        }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-user  { color: #94a3b8; font-size: 0.85rem; }
        .logout-btn {
            background: none; border: 1px solid #334155;
            color: #94a3b8; padding: 0.35rem 0.85rem;
            border-radius: 7px; font-size: 0.82rem; cursor: pointer;
            transition: border-color 0.15s, color 0.15s;
        }
        .logout-btn:hover { border-color: #0891b2; color: #22d3ee; }
        .main { padding: 2rem; max-width: 1100px; margin: 0 auto; }
        .page-header { margin-bottom: 2rem; }
        .page-header h2 { font-size: 1.4rem; font-weight: 700; }
        .page-header p  { color: #64748b; font-size: 0.9rem; margin-top: 0.2rem; }
        .section-title {
            font-size: 0.78rem; font-weight: 600; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;
        }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap: 1rem; margin-bottom: 2rem; }
        .card {
            background: #1e293b; border: 1px solid #334155;
            border-radius: 12px; padding: 1.25rem;
        }
        .card-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .card-icon {
            width: 38px; height: 38px;
            background: rgba(8,145,178,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .card-icon svg { color: #22d3ee; }
        .card-name { font-weight: 600; font-size: 0.95rem; }
        .card-meta { font-size: 0.78rem; color: #64748b; margin-top: 0.15rem; }
        .badge {
            display: inline-block; background: #0f172a;
            border: 1px solid #334155; color: #94a3b8;
            font-size: 0.72rem; font-weight: 600;
            padding: 0.15rem 0.5rem; border-radius: 5px;
            font-family: monospace; margin-right: 0.3rem; margin-top: 0.5rem;
        }
        table {
            width: 100%; border-collapse: collapse;
            background: #1e293b; border: 1px solid #334155;
            border-radius: 12px; overflow: hidden;
        }
        th {
            background: #0f172a; padding: 0.7rem 1rem;
            text-align: left; font-size: 0.78rem;
            color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        }
        td { padding: 0.7rem 1rem; font-size: 0.88rem; border-top: 1px solid #334155; }
        tr:first-child td { border-top: none; }
        .year-pill {
            background: rgba(8,145,178,0.15);
            color: #22d3ee; font-size: 0.75rem; font-weight: 600;
            padding: 0.15rem 0.6rem; border-radius: 20px;
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
            <span class="role-pill">Teacher</span>
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
            <p>Teaching at {{ $schools->count() }} {{ Str::plural('school', $schools->count()) }}</p>
        </div>

        {{-- Schools --}}
        <div class="section-title">Your Schools</div>
        @if ($schools->isEmpty())
            <p style="color:#64748b;font-size:0.9rem;margin-bottom:2rem;">No schools assigned.</p>
        @else
            <div class="grid">
                @foreach ($schools as $school)
                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627
                                             48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482
                                             0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902
                                             0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482
                                             0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75
                                             0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007
                                             11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                                </svg>
                            </div>
                            <div>
                                <div class="card-name">{{ $school->name }}</div>
                                <div class="card-meta">{{ $school->region }}</div>
                            </div>
                        </div>
                        <span class="badge">{{ $school->code }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Class Assignments --}}
        @if ($assignments->isNotEmpty())
            <div class="section-title" style="margin-top:0.5rem;">Class Assignments</div>
            <table>
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Year Group</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $a)
                        <tr>
                            <td>{{ $a->school->name ?? '—' }}</td>
                            <td><span class="year-pill">{{ $a->school_year }}</span></td>
                            <td>{{ $a->class_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>
