@extends('superadmin.layouts.app')

@section('title', 'Add Question')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1>Add Question</h1>
        <p>Create a new MCQ question for the centralised question bank.</p>
    </div>
    <a href="{{ route('superadmin.questions.index') }}" class="btn btn-outline">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Question Bank
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please fix the errors below:</strong>
        <ul style="margin:.4rem 0 0 1.1rem;font-size:.83rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@include('superadmin.questions._form')

@endsection
