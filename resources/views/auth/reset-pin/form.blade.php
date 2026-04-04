@extends('auth.layouts.auth')

@section('title', 'Set New PIN')

@section('content')
    <div class="card-title">Set a new PIN</div>
    <div class="card-subtitle">Choose a new 6-digit PIN for your account</div>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('pin.reset.update') }}" id="pin-form">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        {{-- New PIN --}}
        <div class="form-group" style="text-align:center;">
            <label>New PIN</label>
            <input type="hidden" name="pin" id="pin-hidden">
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

        {{-- Confirm PIN --}}
        <div class="form-group" style="text-align:center;margin-top:1rem;">
            <label>Confirm New PIN</label>
            <input type="hidden" name="pin_confirmation" id="pin-confirm-hidden">
            <div class="pin-wrap" id="pin-confirm-boxes">
                @for ($i = 0; $i < 6; $i++)
                    <input
                        type="password"
                        inputmode="numeric"
                        maxlength="1"
                        class="pin-confirm-digit"
                        autocomplete="off"
                        data-index="{{ $i }}"
                    >
                @endfor
            </div>
            <div id="pin-mismatch" class="field-error" style="text-align:center;margin-top:0.5rem;display:none;">
                PINs do not match.
            </div>
        </div>

        <button type="submit" class="btn" id="save-btn" style="margin-top:1.25rem;" disabled>Save New PIN</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login') }}">&larr; Back to sign in</a>
@endsection

@push('scripts')
<script>
    function makeDigitHandler(digits, hiddenInput, onChange) {
        digits.forEach((input, idx) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/, '').slice(-1);
                hiddenInput.value = digits.map(d => d.value).join('');
                if (input.value && idx < digits.length - 1) digits[idx + 1].focus();
                onChange();
            });
            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !input.value && idx > 0) digits[idx - 1].focus();
            });
            input.addEventListener('paste', e => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData)
                    .getData('text').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((ch, i) => { if (digits[i]) digits[i].value = ch; });
                digits[Math.min(pasted.length, digits.length - 1)].focus();
                hiddenInput.value = digits.map(d => d.value).join('');
                onChange();
            });
        });
    }

    const pinDigits    = Array.from(document.querySelectorAll('.pin-digit'));
    const confirmDigits= Array.from(document.querySelectorAll('.pin-confirm-digit'));
    const pinHidden    = document.getElementById('pin-hidden');
    const confirmHidden= document.getElementById('pin-confirm-hidden');
    const saveBtn      = document.getElementById('save-btn');
    const mismatch     = document.getElementById('pin-mismatch');

    function validate() {
        const pin     = pinHidden.value;
        const confirm = confirmHidden.value;
        const match   = pin === confirm;
        mismatch.style.display = (confirm.length === 6 && !match) ? '' : 'none';
        saveBtn.disabled = !(pin.length === 6 && confirm.length === 6 && match);
    }

    makeDigitHandler(pinDigits,     pinHidden,     validate);
    makeDigitHandler(confirmDigits, confirmHidden, validate);

    pinDigits[0].focus();
</script>
@endpush
