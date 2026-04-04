@extends('auth.layouts.auth')

@section('title', 'Sign In — Select Role')
@section('portal-label', 'School Portal')

@section('steps')
<div class="steps">
    <div class="step active">
        <div class="step-dot">1</div>
        <span>Role</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
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
    <div class="card-title">Welcome back</div>
    <div class="card-subtitle">Select your role to continue</div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.role') }}" id="role-form">
        @csrf

        <div class="role-grid">
            {{-- School Admin card --}}
            <label class="role-card" id="card-admin">
                <input type="radio" name="role" value="school_admin" id="role-admin">
                <div class="role-icon">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5
                                 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125
                                 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
                <span class="role-label">School Admin</span>
            </label>

            {{-- Teacher card --}}
            <label class="role-card" id="card-teacher">
                <input type="radio" name="role" value="teacher" id="role-teacher">
                <div class="role-icon">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0
                                 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0
                                 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905
                                 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482
                                 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75
                                 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378
                                 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <span class="role-label">Teacher</span>
            </label>
        </div>

        <button type="submit" class="btn" id="continue-btn" disabled>Continue</button>
    </form>
@endsection

@push('scripts')
<script>
    const adminCard   = document.getElementById('card-admin');
    const teacherCard = document.getElementById('card-teacher');
    const adminRadio  = document.getElementById('role-admin');
    const teacherRadio= document.getElementById('role-teacher');
    const btn         = document.getElementById('continue-btn');

    function select(card, radio, other) {
        card.classList.add('selected');
        other.classList.remove('selected');
        radio.checked = true;
        btn.disabled  = false;
    }

    adminCard.addEventListener('click',   () => select(adminCard,   adminRadio,   teacherCard));
    teacherCard.addEventListener('click', () => select(teacherCard, teacherRadio, adminCard));
</script>
@endpush
