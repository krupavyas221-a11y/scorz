@extends('superadmin.layouts.app')

@section('title', 'Create Class')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Create Class</h1>
        <p>Define a new class that can be assigned to teachers.</p>
    </div>
    <a href="{{ route('superadmin.classes.index') }}" class="btn btn-outline">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
</div>

<div class="card" style="max-width:520px">
    <div class="card-header">
        <h2>Class Details</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('superadmin.classes.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Class Name <span style="color:var(--danger)">*</span></label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control"
                       placeholder="e.g., Junior Infant, Second Class"
                       value="{{ old('name') }}"
                       maxlength="60"
                       autocomplete="off">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
                <div style="color:var(--muted);font-size:.76rem;margin-top:.3rem">
                    Examples: Junior Infant, Senior Infant, 1st Class, 2nd Class…
                </div>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.5rem">
                <button type="submit" class="btn btn-primary">Create Class</button>
                <a href="{{ route('superadmin.classes.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
