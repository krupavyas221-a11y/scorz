<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Scorz Admin</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:       #0f172a;
            --surface:  #1e293b;
            --border:   #334155;
            --text:     #e2e8f0;
            --muted:    #64748b;
            --accent:   #6366f1;
            --accent-h: #4f46e5;
            --success:  #22c55e;
            --danger:   #ef4444;
            --warning:  #f59e0b;
            --sidebar-w: 240px;
        }
        html, body { height: 100%; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
               background: var(--bg); color: var(--text); display: flex; flex-direction: column; }

        /* ── Topbar ── */
        .topbar {
            height: 56px; background: var(--surface); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 1.25rem; gap: 1rem;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-brand { font-weight: 800; font-size: 1.05rem; color: #f1f5f9; display:flex; align-items:center; gap:.5rem; }
        .topbar-brand span { background:var(--accent); border-radius:7px; width:28px; height:28px;
            display:inline-flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem; }
        .topbar-right { margin-left: auto; display:flex; align-items:center; gap:.85rem; font-size:.82rem; color:var(--muted); }
        .topbar-right strong { color: var(--text); }
        .btn-logout { background:none; border:1px solid var(--border); color:var(--muted); padding:.3rem .8rem;
            border-radius:6px; cursor:pointer; font-size:.8rem; transition:all .15s; }
        .btn-logout:hover { border-color:var(--accent); color:#818cf8; }

        /* ── Layout ── */
        .layout { display:flex; flex:1; min-height:0; }

        /* ── Sidebar ── */
        .sidebar { width:var(--sidebar-w); background:var(--surface); border-right:1px solid var(--border);
            padding:1.25rem 0; flex-shrink:0; overflow-y:auto; }
        .nav-section { padding: 0 .75rem .5rem; }
        .nav-label { font-size:.68rem; font-weight:600; color:var(--muted); text-transform:uppercase;
            letter-spacing:.08em; padding: .5rem .75rem .25rem; }
        .nav-item { display:flex; align-items:center; gap:.6rem; padding:.5rem .75rem;
            border-radius:7px; color:var(--muted); font-size:.83rem; font-weight:500;
            text-decoration:none; transition:all .15s; cursor:pointer; margin-bottom:2px; }
        .nav-item:hover { background: rgba(99,102,241,.1); color:var(--text); }
        .nav-item.active { background: rgba(99,102,241,.18); color: #818cf8; }
        .nav-item svg { width:16px; height:16px; flex-shrink:0; }

        /* ── Main ── */
        .main { flex:1; overflow-y:auto; padding:1.75rem 2rem; }
        .page-header { margin-bottom:1.5rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; }
        .page-header-left h1 { font-size:1.2rem; font-weight:700; color:#f1f5f9; }
        .page-header-left p  { color:var(--muted); font-size:.83rem; margin-top:.2rem; }

        /* ── Buttons ── */
        .btn { display:inline-flex; align-items:center; gap:.4rem; padding:.48rem 1rem;
            border-radius:7px; font-size:.82rem; font-weight:600; cursor:pointer;
            border:none; text-decoration:none; transition:all .15s; }
        .btn-primary  { background:var(--accent); color:#fff; }
        .btn-primary:hover  { background:var(--accent-h); }
        .btn-outline  { background:none; border:1px solid var(--border); color:var(--muted); }
        .btn-outline:hover  { border-color:var(--accent); color:#818cf8; }
        .btn-danger   { background:var(--danger); color:#fff; }
        .btn-danger:hover   { background:#dc2626; }
        .btn-success  { background:#16a34a; color:#fff; }
        .btn-success:hover  { background:#15803d; }
        .btn-sm { padding:.3rem .7rem; font-size:.76rem; }

        /* ── Card ── */
        .card { background:var(--surface); border:1px solid var(--border); border-radius:12px; }
        .card-header { padding:1rem 1.25rem; border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .card-header h2 { font-size:.95rem; font-weight:600; color:#f1f5f9; }
        .card-body { padding:1.25rem; }

        /* ── Table ── */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.83rem; }
        th { text-align:left; padding:.6rem .9rem; color:var(--muted); font-weight:600;
             font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;
             border-bottom:1px solid var(--border); white-space:nowrap; }
        td { padding:.7rem .9rem; border-bottom:1px solid rgba(51,65,85,.5); color:var(--text); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background: rgba(99,102,241,.04); }

        /* ── Badges ── */
        .badge { display:inline-flex; align-items:center; padding:.2rem .6rem;
            border-radius:999px; font-size:.72rem; font-weight:600; }
        .badge-success { background:rgba(34,197,94,.15); color:#4ade80; }
        .badge-danger  { background:rgba(239,68,68,.15);  color:#f87171; }
        .badge-gray    { background:rgba(100,116,139,.15); color:#94a3b8; }
        .badge-blue    { background:rgba(99,102,241,.15);  color:#818cf8; }

        /* ── Form elements ── */
        .form-group { margin-bottom:1rem; }
        .form-group label { display:block; color:#cbd5e1; font-size:.8rem; font-weight:500; margin-bottom:.35rem; }
        .form-control {
            width:100%; background:#0f172a; border:1px solid var(--border); border-radius:7px;
            padding:.55rem .8rem; color:#f1f5f9; font-size:.875rem; outline:none; transition:border-color .15s;
        }
        .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(99,102,241,.12); }
        .form-control::placeholder { color:#475569; }
        select.form-control { cursor:pointer; }
        .field-error { color:#f87171; font-size:.76rem; margin-top:.25rem; }

        /* ── Alerts ── */
        .alert { padding:.75rem 1rem; border-radius:8px; font-size:.84rem; margin-bottom:1rem; }
        .alert-success { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); color:#4ade80; }
        .alert-error   { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25);  color:#f87171; }

        /* ── Toolbar (search/filter) ── */
        .toolbar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.25rem; }
        .toolbar-search { position:relative; }
        .toolbar-search input { padding-left:2.1rem; }
        .toolbar-search .icon { position:absolute; left:.65rem; top:50%; transform:translateY(-50%);
            color:var(--muted); pointer-events:none; }

        /* ── Pagination ── */
        .pagination { display:flex; align-items:center; gap:.3rem; margin-top:1.25rem; font-size:.82rem; }
        .pagination a, .pagination span {
            padding:.3rem .65rem; border-radius:6px; border:1px solid var(--border);
            color:var(--muted); text-decoration:none; transition:all .15s;
        }
        .pagination a:hover { border-color:var(--accent); color:#818cf8; }
        .pagination .active span { background:var(--accent); border-color:var(--accent); color:#fff; }
        .pagination .disabled span { opacity:.4; }

        /* ── Grid ── */
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
        @media(max-width:768px) { .grid-2,.grid-3 { grid-template-columns:1fr; } }

        /* ── Divider ── */
        .section-title { font-size:.78rem; font-weight:700; color:var(--muted); text-transform:uppercase;
            letter-spacing:.07em; margin:1.5rem 0 .75rem; padding-bottom:.4rem;
            border-bottom:1px solid var(--border); }
    </style>
    @stack('styles')
</head>
<body>

{{-- Topbar --}}
<header class="topbar">
    <div class="topbar-brand">
        <span>S</span> Scorz
    </div>
    <div class="topbar-right">
        <span>Hello, <strong>{{ Auth::guard('superadmin')->user()->name }}</strong></span>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</header>

<div class="layout">
    {{-- Sidebar --}}
    <nav class="sidebar">
        <div class="nav-section">
            <div class="nav-label">Main</div>
            <a href="{{ route('superadmin.dashboard') }}"
               class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Master Data</div>
            @php
                $masterNav = [
                    ['label' => 'Subjects',         'route' => 'superadmin.subjects.index'],
                    ['label' => 'Strands',          'route' => 'superadmin.strands.index'],
                    ['label' => 'Skill Categories', 'route' => 'superadmin.skill-categories.index'],
                    ['label' => 'Test Types',       'route' => 'superadmin.test-types.index'],
                    ['label' => 'Seasons',          'route' => 'superadmin.seasons.index'],
                    ['label' => 'Test Levels',      'route' => 'superadmin.test-levels.index'],
                ];
            @endphp
            @foreach($masterNav as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
        <div class="nav-section">
            <div class="nav-label">Management</div>
            <a href="{{ route('superadmin.classes.index') }}"
               class="nav-item {{ request()->routeIs('superadmin.classes.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                </svg>
                Classes
            </a>
            <a href="{{ route('superadmin.school-years.index') }}"
               class="nav-item {{ request()->routeIs('superadmin.school-years.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                School Years
            </a>
            <a href="{{ route('superadmin.school-admins.index') }}"
               class="nav-item {{ request()->routeIs('superadmin.school-admins.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
                </svg>
                School Admins
            </a>
            <a href="{{ route('superadmin.teachers.index') }}"
               class="nav-item {{ request()->routeIs('superadmin.teachers.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                </svg>
                Teachers
            </a>
            <a href="{{ route('superadmin.pupils.index') }}"
               class="nav-item {{ request()->routeIs('superadmin.pupils.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                Pupils
            </a>
        </div>
    </nav>

    {{-- Main content --}}
    <main class="main">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
