@extends('auth.layouts.auth')

@section('title', 'Sign In — Credentials')

@section('steps')
<div class="steps">
    <div class="step done">
        <div class="step-dot">&#10003;</div>
        <span>Role</span>
    </div>
    <div class="step-line done"></div>
    <div class="step active">
        <div class="step-dot">2</div>
        <span>Credentials</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
        <div class="step-dot">3</div>
        <span>PIN</span>
    </div>
</div>
@endsection

@section('content')
    @php
        $roleLabel = $role === 'school_admin' ? 'School Admin' : 'Teacher';
    @endphp

    <div class="context-badge">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0
                     00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963
                     0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15
                     9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ $roleLabel }}
    </div>

    <div class="card-title">Enter your credentials</div>
    <div class="card-subtitle">Sign in as {{ $roleLabel }}</div>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.credentials') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@school.edu"
                required
                autofocus
                autocomplete="email"
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="eye-btn" onclick="togglePwd()" aria-label="Toggle password">
                    <svg id="eye-on" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0
                                 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12
                                 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg id="eye-off" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12
                                 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756
                                 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293
                                 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21
                                 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <span></span>
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        <button type="submit" class="btn">Continue</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login') }}">&larr; Change role</a>
@endsection

@push('scripts')
<script>
function togglePwd() {
    const inp = document.getElementById('password');
    const on  = document.getElementById('eye-on');
    const off = document.getElementById('eye-off');
    const vis = inp.type === 'text';
    inp.type = vis ? 'password' : 'text';
    on.style.display  = vis ? '' : 'none';
    off.style.display = vis ? 'none' : '';
}
</script>
@endpush
