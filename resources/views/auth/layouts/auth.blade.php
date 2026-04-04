<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — Scorz</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
        }

        /* Brand */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 52px;
            height: 52px;
            background: #6366f1;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        .brand-icon svg { color: #fff; }
        .brand h1 {
            color: #f8fafc;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .brand p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 0.2rem;
        }

        /* Step indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 1.75rem;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            color: #475569;
        }
        .step-dot {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #1e293b;
            border: 2px solid #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            flex-shrink: 0;
        }
        .step.active .step-dot {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
        }
        .step.done .step-dot {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }
        .step-line {
            width: 36px;
            height: 2px;
            background: #334155;
            flex-shrink: 0;
        }
        .step-line.done { background: #059669; }

        /* Card */
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 2rem;
        }
        .card-title {
            color: #f1f5f9;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .card-subtitle {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 1.75rem;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .alert-success {
            background: #052e16;
            border: 1px solid #166534;
            color: #86efac;
        }
        .alert-error {
            background: #2d0a0a;
            border: 1px solid #7f1d1d;
            color: #fca5a5;
        }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        label {
            display: block;
            color: #cbd5e1;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="number"] {
            width: 100%;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            color: #f1f5f9;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.15s;
        }
        input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        input::placeholder { color: #475569; }
        .field-error {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 0.3rem;
        }

        /* Password wrapper */
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 2.8rem; }
        .eye-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.15s;
        }
        .eye-btn:hover { color: #94a3b8; }

        /* PIN input */
        .pin-wrap {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        .pin-wrap input {
            width: 48px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 0.65rem 0;
            letter-spacing: 0.1em;
        }

        /* Row: remember + forgot */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #94a3b8;
            font-size: 0.82rem;
            cursor: pointer;
        }
        .checkbox-label input[type="checkbox"] {
            width: auto;
            accent-color: #6366f1;
        }

        /* Role cards */
        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .role-card {
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 1.25rem 1rem;
            cursor: pointer;
            text-align: center;
            transition: border-color 0.15s, background 0.15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        .role-card:hover {
            border-color: #6366f1;
            background: #1e1b4b;
        }
        .role-card input[type="radio"] { display: none; }
        .role-card input[type="radio"]:checked + .role-inner {
            /* handled via JS class toggle */
        }
        .role-card.selected {
            border-color: #6366f1;
            background: #1e1b4b;
        }
        .role-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .role-card.selected .role-icon { background: rgba(99,102,241,0.2); }
        .role-icon svg { color: #6366f1; }
        .role-label {
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .role-card.selected .role-label { color: #818cf8; }

        /* Links */
        a { color: #818cf8; text-decoration: none; font-size: 0.82rem; }
        a:hover { color: #a5b4fc; text-decoration: underline; }

        /* Button */
        .btn {
            width: 100%;
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            letter-spacing: 0.01em;
        }
        .btn:hover { background: #4f46e5; }
        .btn:active { transform: scale(0.99); }
        .btn:disabled {
            background: #334155;
            color: #64748b;
            cursor: not-allowed;
        }

        /* Back / footer links */
        .back-link {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.82rem;
            color: #64748b;
        }
        .back-link a { color: #818cf8; }

        /* Context badge */
        .context-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.78rem;
            color: #818cf8;
            margin-bottom: 1rem;
        }
        .context-badge svg { width: 13px; height: 13px; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="brand">
            <div class="brand-icon">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6
                             2.25 2.25 0 003 8.118v1.632c0 5.193 3.663 10.003 8.723 11.228
                             .02.005.04.009.06.013A11.962 11.962 0 0021 9.75v-1.632a2.25
                             2.25 0 00-.598-1.518A11.959 11.959 0 0112 2.714z"/>
                </svg>
            </div>
            <h1>Scorz</h1>
            <p>@yield('portal-label', 'School Portal')</p>
        </div>

        @hasSection('steps')
            @yield('steps')
        @endif

        <div class="card">
            @yield('content')
        </div>

        @hasSection('footer')
            <div class="back-link">@yield('footer')</div>
        @endif
    </div>
    @stack('scripts')
</body>
</html>
