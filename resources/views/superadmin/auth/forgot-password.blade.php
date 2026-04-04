@extends('superadmin.layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="card-title">Forgot your password?</div>
    <div class="card-subtitle">
        Enter your registered email and we'll send you a reset link.
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('superadmin.password.email') }}">
        @csrf

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

        <button type="submit" class="btn">Send Reset Link</button>
    </form>
@endsection

@section('footer')
    Remember your password? <a href="{{ route('superadmin.login') }}">Back to login</a>
@endsection
