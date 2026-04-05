@extends('superadmin.layouts.app')

@section('title', 'Edit Question — ' . $question->question_id)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1>Edit Question</h1>
        <p>
            <span style="font-family:monospace;color:#818cf8;font-weight:700">
                {{ $question->question_id }}
            </span>
            @if($question->is_active)
                <span class="badge badge-success" style="margin-left:.5rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.5rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST"
              action="{{ route('superadmin.questions.toggle-status', $question) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $question->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $question->is_active ? 'Deactivate' : 'Activate' }} this question?')">
                {{ $question->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>

        <form method="POST"
              action="{{ route('superadmin.questions.destroy', $question) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Permanently delete question {{ $question->question_id }}?')">
                Delete
            </button>
        </form>

        <a href="{{ route('superadmin.questions.index') }}" class="btn btn-outline">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please fix the errors below:</strong>
        <ul style="margin:.4rem 0 0 1.1rem;font-size:.83rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Quick preview card --}}
<div class="card" style="margin-bottom:1.25rem;border-color:rgba(99,102,241,.3)">
    <div class="card-body" style="padding:.9rem 1.1rem">
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.8rem">
            <div>
                <span style="color:var(--muted)">Subject:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ $question->subject?->name ?? '—' }}</strong>
            </div>
            <div>
                <span style="color:var(--muted)">Class:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ $question->schoolClass?->name ?? '—' }}</strong>
            </div>
            <div>
                <span style="color:var(--muted)">Strand:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ $question->strand?->name ?? '—' }}</strong>
            </div>
            <div>
                <span style="color:var(--muted)">Marks:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ $question->marks }}</strong>
            </div>
            <div>
                <span style="color:var(--muted)">Options:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ count($question->options) }}</strong>
            </div>
            <div>
                <span style="color:var(--muted)">Correct:</span>
                <strong style="color:#4ade80;margin-left:.3rem">
                    {{ $question->correct_option_letter }} — {{ $question->correct_option_text }}
                </strong>
            </div>
            <div style="margin-left:auto">
                <span style="color:var(--muted)">Created:</span>
                <strong style="color:#f1f5f9;margin-left:.3rem">{{ $question->created_at->format('d M Y') }}</strong>
            </div>
        </div>
    </div>
</div>

@include('superadmin.questions._form')

@endsection
