@extends('auth.layouts.auth')

@section('title', 'Sign In — PIN')

@section('steps')
<div class="steps">
    <div class="step done">
        <div class="step-dot">&#10003;</div>
        <span>Role</span>
    </div>
    <div class="step-line done"></div>
    <div class="step done">
        <div class="step-dot">&#10003;</div>
        <span>Credentials</span>
    </div>
    <div class="step-line done"></div>
    <div class="step active">
        <div class="step-dot">3</div>
        <span>PIN</span>
    </div>
</div>
@endsection

@section('content')
    <div class="card-title">Enter your PIN</div>
    <div class="card-subtitle">Enter your 6-digit security PIN to complete sign-in</div>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.pin') }}" id="pin-form">
        @csrf

        {{-- Hidden field holds the assembled PIN --}}
        <input type="hidden" name="pin" id="pin-hidden">

        <div class="form-group" style="text-align:center;">
            <label style="margin-bottom:0.75rem;">6-Digit PIN</label>
            <div class="pin-wrap" id="pin-boxes">
                @for ($i = 0; $i < 6; $i++)
                    <input
                        type="password"
                        inputmode="numeric"
                        maxlength="1"
                        class="pin-digit"
                        autocomplete="off"
                        data-index="{{ $i }}"
                    >
                @endfor
            </div>
            @error('pin')
                <div class="field-error" style="text-align:center;margin-top:0.5rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="text-align:right;margin-bottom:1.5rem;">
            <a href="{{ route('pin.reset.request') }}">Forgot PIN?</a>
        </div>

        <button type="submit" class="btn" id="pin-submit" disabled>Verify &amp; Sign In</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login.credentials') }}">&larr; Back</a>
@endsection

@push('scripts')
<script>
    const digits  = Array.from(document.querySelectorAll('.pin-digit'));
    const hidden  = document.getElementById('pin-hidden');
    const submit  = document.getElementById('pin-submit');

    function updateHidden() {
        const val = digits.map(d => d.value).join('');
        hidden.value  = val;
        submit.disabled = val.length < 6;
    }

    digits.forEach((input, idx) => {
        input.addEventListener('input', e => {
            // Allow only digits
            input.value = input.value.replace(/\D/, '').slice(-1);
            updateHidden();
            if (input.value && idx < digits.length - 1) {
                digits[idx + 1].focus();
            }
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                digits[idx - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', e => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((ch, i) => {
                if (digits[i]) digits[i].value = ch;
            });
            const next = Math.min(pasted.length, digits.length - 1);
            digits[next].focus();
            updateHidden();
        });
    });

    digits[0].focus();
</script>
@endpush
