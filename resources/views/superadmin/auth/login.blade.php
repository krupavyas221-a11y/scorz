@extends('superadmin.layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="card-title">Welcome back</div>
    <div class="card-subtitle">Sign in to your Super Admin account</div>

    {{-- Session status (e.g. after password reset) --}}
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Auth error --}}
    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('superadmin.login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@example.com"
                required
                autofocus
                autocomplete="email"
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
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
                <button type="button" class="eye-btn" onclick="togglePassword()" aria-label="Toggle password">
                    <svg id="eye-icon" width="18" height="18" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.036 12.322a1.012 1.012 0 010-.639
                                 C3.423 7.51 7.36 4.5 12 4.5c4.638 0
                                 8.573 3.007 9.963 7.178.07.207.07.431
                                 0 .639C20.577 16.49 16.64 19.5 12
                                 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <svg id="eye-off-icon" width="18" height="18" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.98 8.223A10.477 10.477 0 001.934
                                 12C3.226 16.338 7.244 19.5 12 19.5c.993
                                 0 1.953-.138 2.863-.395M6.228 6.228A10.45
                                 10.45 0 0112 4.5c4.756 0 8.773 3.162
                                 10.065 7.498a10.523 10.523 0 01-4.293
                                 5.774M6.228 6.228L3 3m3.228 3.228l3.65
                                 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0
                                 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88
                                 9.88"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="form-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="{{ route('superadmin.password.request') }}">Forgot password?</a>
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>
@endsection

@section('footer')
    &copy; {{ date('Y') }} Scorz. All rights reserved.
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input   = document.getElementById('password');
    const eyeOn   = document.getElementById('eye-icon');
    const eyeOff  = document.getElementById('eye-off-icon');
    const visible = input.type === 'text';

    input.type    = visible ? 'password' : 'text';
    eyeOn.style.display  = visible ? '' : 'none';
    eyeOff.style.display = visible ? 'none' : '';
}
</script>
@endpush
