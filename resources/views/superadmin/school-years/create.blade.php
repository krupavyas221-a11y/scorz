@extends('superadmin.layouts.app')

@section('title', 'Create School Year')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Create School Year</h1>
        <p>Add a new academic year to the system.</p>
    </div>
    <a href="{{ route('superadmin.school-years.index') }}" class="btn btn-outline">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
</div>

<div class="card" style="max-width:520px">
    <div class="card-header">
        <h2>Academic Year Details</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('superadmin.school-years.store') }}">
            @csrf

            <div class="form-group">
                <label for="year">Academic Year <span style="color:var(--danger)">*</span></label>
                <input type="text"
                       id="year"
                       name="year"
                       class="form-control"
                       placeholder="e.g., 2023–2024"
                       value="{{ old('year') }}"
                       maxlength="20"
                       autocomplete="off">
                @error('year')
                    <div class="field-error">{{ $message }}</div>
                @enderror
                <div style="color:var(--muted);font-size:.76rem;margin-top:.3rem">
                    Format: YYYY–YYYY (e.g., 2023–2024 or 2023-2024)
                </div>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-primary">Create School Year</button>
                <a href="{{ route('superadmin.school-years.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
