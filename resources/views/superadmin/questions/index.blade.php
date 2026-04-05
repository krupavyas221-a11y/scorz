@extends('superadmin.layouts.app')

@section('title', 'Question Bank')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1>Question Bank</h1>
        <p>Centralised repository of MCQ questions used across all schools.</p>
    </div>
    <a href="{{ route('superadmin.questions.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add Question
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('superadmin.questions.index') }}">
    <div class="toolbar" style="flex-wrap:wrap;gap:.6rem">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:230px;padding-left:2.1rem"
                   placeholder="Search question text or ID…" value="{{ request('search') }}">
        </div>

        <select name="subject_id" class="form-control" style="width:150px">
            <option value="">All Subjects</option>
            @foreach($filterData['subjects'] as $s)
                <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>

        <select name="class_id" class="form-control" style="width:140px">
            <option value="">All Classes</option>
            @foreach($filterData['classes'] as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>

        <select name="strand_id" class="form-control" style="width:140px">
            <option value="">All Strands</option>
            @foreach($filterData['strands'] as $st)
                <option value="{{ $st->id }}" {{ request('strand_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
            @endforeach
        </select>

        <select name="test_type_id" class="form-control" style="width:150px">
            <option value="">All Test Types</option>
            @foreach($filterData['testTypes'] as $tt)
                <option value="{{ $tt->id }}" {{ request('test_type_id') == $tt->id ? 'selected' : '' }}>{{ $tt->name }}</option>
            @endforeach
        </select>

        <select name="status" class="form-control" style="width:130px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search','subject_id','class_id','strand_id','test_type_id','status']))
            <a href="{{ route('superadmin.questions.index') }}" class="btn btn-outline">Clear</a>
        @endif

        <span style="margin-left:auto;color:var(--muted);font-size:.8rem">
            {{ $questions->total() }} {{ Str::plural('question', $questions->total()) }}
        </span>
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Question</th>
                    <th>Subject</th>
                    <th>Class</th>
                    <th>Strand</th>
                    <th>Skill</th>
                    <th>Test Type</th>
                    <th style="width:60px">Marks</th>
                    <th>Difficulty</th>
                    <th>Status</th>
                    <th style="width:190px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-size:.8rem;color:#818cf8;font-weight:700">
                                {{ $q->question_id }}
                            </span>
                        </td>
                        <td style="max-width:260px">
                            <div style="font-size:.82rem;color:#f1f5f9;
                                        overflow:hidden;display:-webkit-box;
                                        -webkit-line-clamp:2;-webkit-box-orient:vertical">
                                {{ $q->question_text }}
                            </div>
                            <div style="font-size:.71rem;color:var(--muted);margin-top:2px">
                                MCQ · {{ count($q->options) }} options ·
                                Ans: <strong style="color:#4ade80">{{ $q->correct_option_letter }}</strong>
                            </div>
                        </td>
                        <td>
                            @if($q->subject)
                                <span class="badge badge-blue">{{ $q->subject->name }}</span>
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem">{{ $q->schoolClass?->name ?? '—' }}</td>
                        <td style="font-size:.82rem">{{ $q->strand?->name ?? '—' }}</td>
                        <td style="font-size:.82rem">{{ $q->skillCategory?->name ?? '—' }}</td>
                        <td style="font-size:.82rem">{{ $q->testType?->name ?? '—' }}</td>
                        <td style="font-weight:700;color:#f1f5f9;text-align:center">
                            {{ number_format($q->marks, $q->marks == floor($q->marks) ? 0 : 2) }}
                        </td>
                        <td>
                            @if($q->testLevel)
                                @php
                                    $lvl = strtolower($q->testLevel->name);
                                    $badgeClass = str_contains($lvl,'easy') ? 'badge-success'
                                        : (str_contains($lvl,'hard') ? 'badge-danger' : 'badge-gray');
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $q->testLevel->name }}</span>
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td>
                            @if($q->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:.3rem;flex-wrap:wrap">
                                <a href="{{ route('superadmin.questions.edit', $q) }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('superadmin.questions.toggle-status', $q) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $q->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $q->is_active ? 'Deactivate' : 'Activate' }} question {{ $q->question_id }}?')">
                                        {{ $q->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('superadmin.questions.destroy', $q) }}"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete question {{ $q->question_id }}?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:3rem;color:var(--muted)">
                            No questions found.
                            <a href="{{ route('superadmin.questions.create') }}"
                               style="color:var(--accent);text-decoration:none;margin-left:.4rem">
                                Add the first question.
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questions->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $questions->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>

@endsection
