@extends('auth.layouts.auth')

@section('title', 'Reset PIN')

@section('content')
    <div class="card-title">Reset your PIN</div>
    <div class="card-subtitle">Enter your email and we'll send you a PIN reset link</div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('pin.reset.send') }}">
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

        <button type="submit" class="btn">Send PIN Reset Link</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login') }}">&larr; Back to sign in</a>
@endsection
