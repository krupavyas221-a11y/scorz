@push('styles')
<style>
    /* ── MCQ option rows ── */
    .option-row {
        display: flex; align-items: center; gap: .6rem;
        margin-bottom: .5rem;
    }
    .option-letter {
        width: 28px; height: 28px; flex-shrink: 0;
        background: rgba(99,102,241,.15); color: #818cf8;
        border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-weight: 700; font-size: .8rem;
    }
    .option-row input[type="text"] { flex: 1; }
    .option-radio-wrap {
        display: flex; align-items: center; gap: .35rem;
        font-size: .78rem; color: var(--muted); white-space: nowrap; cursor: pointer;
    }
    .option-radio-wrap input[type="radio"] {
        accent-color: #22c55e; width: 15px; height: 15px; cursor: pointer;
    }
    .option-radio-wrap.selected { color: #4ade80; }
    .remove-option-btn {
        background: none; border: none; color: var(--muted);
        cursor: pointer; font-size: 1rem; padding: .2rem .4rem;
        border-radius: 4px; line-height: 1; transition: color .15s;
    }
    .remove-option-btn:hover { color: var(--danger); }

    /* ── Correct answer indicator ── */
    .correct-answer-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .75rem; color: #4ade80; font-weight: 600;
        background: rgba(34,197,94,.1); padding: .25rem .6rem;
        border-radius: 6px; border: 1px solid rgba(34,197,94,.2);
    }
</style>
@endpush

@php
    $isEdit    = isset($question);
    $action    = $isEdit
        ? route('superadmin.questions.update', $question)
        : route('superadmin.questions.store');
    $oldOpts   = old('options',  $isEdit ? $question->options        : ['', '', '', '']);
    $oldAnswer = old('correct_answer', $isEdit ? $question->correct_answer : 0);
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ── Card 1: Context ──────────────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Question Context</h2></div>
        <div class="card-body">

            <div class="grid-3">
                <div class="form-group">
                    <label>Subject <span style="color:var(--danger)">*</span></label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Select subject…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}"
                                {{ old('subject_id', $isEdit ? $question->subject_id : '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Class Level</label>
                    <select name="class_id" class="form-control">
                        <option value="">Select class…</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}"
                                {{ old('class_id', $isEdit ? $question->class_id : '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Strand</label>
                    <select name="strand_id" class="form-control">
                        <option value="">Select strand…</option>
                        @foreach($strands as $st)
                            <option value="{{ $st->id }}"
                                {{ old('strand_id', $isEdit ? $question->strand_id : '') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('strand_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Skill Category</label>
                    <select name="skill_category_id" class="form-control">
                        <option value="">Select skill…</option>
                        @foreach($skillCategories as $sk)
                            <option value="{{ $sk->id }}"
                                {{ old('skill_category_id', $isEdit ? $question->skill_category_id : '') == $sk->id ? 'selected' : '' }}>
                                {{ $sk->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('skill_category_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Test Type</label>
                    <select name="test_type_id" class="form-control">
                        <option value="">Select test type…</option>
                        @foreach($testTypes as $tt)
                            <option value="{{ $tt->id }}"
                                {{ old('test_type_id', $isEdit ? $question->test_type_id : '') == $tt->id ? 'selected' : '' }}>
                                {{ $tt->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('test_type_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Season</label>
                    <select name="season_id" class="form-control">
                        <option value="">Select season…</option>
                        @foreach($seasons as $se)
                            <option value="{{ $se->id }}"
                                {{ old('season_id', $isEdit ? $question->season_id : '') == $se->id ? 'selected' : '' }}>
                                {{ $se->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('season_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration" class="form-control"
                           value="{{ old('duration', $isEdit ? $question->duration : '') }}"
                           min="1" max="600" placeholder="e.g. 45">
                    @error('duration')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Marking Scheme</label>
                    <input type="text" name="marking_scheme" class="form-control"
                           value="{{ old('marking_scheme', $isEdit ? $question->marking_scheme : '') }}"
                           maxlength="255" placeholder="e.g. 1 mark per correct answer">
                    @error('marking_scheme')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

        </div>
    </div>

    {{-- ── Card 2: Question Content ──────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header">
            <h2>Question Content</h2>
            <span class="badge badge-blue" style="font-size:.7rem">MCQ</span>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label>Question Text <span style="color:var(--danger)">*</span></label>
                <textarea name="question_text" class="form-control" rows="3"
                          maxlength="2000" placeholder="Enter the full question text…" required
                          style="resize:vertical">{{ old('question_text', $isEdit ? $question->question_text : '') }}</textarea>
                @error('question_text')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            {{-- Options --}}
            <div class="section-title" style="margin-top:1.25rem">Answer Options</div>
            <div style="font-size:.78rem;color:var(--muted);margin-bottom:.75rem">
                Add 2–6 options. Click "Correct" to mark the right answer.
            </div>

            @error('options')   <div class="field-error" style="margin-bottom:.5rem">{{ $message }}</div>@enderror
            @error('options.*') <div class="field-error" style="margin-bottom:.5rem">{{ $message }}</div>@enderror
            @error('correct_answer') <div class="field-error" style="margin-bottom:.5rem">{{ $message }}</div>@enderror

            <div id="options-container">
                @foreach($oldOpts as $i => $optText)
                    <div class="option-row" data-index="{{ $i }}">
                        <div class="option-letter">{{ chr(65 + $i) }}</div>
                        <input type="text" name="options[]" class="form-control"
                               value="{{ $optText }}"
                               placeholder="Option {{ chr(65 + $i) }}…" maxlength="500">
                        <label class="option-radio-wrap {{ $oldAnswer == $i ? 'selected' : '' }}">
                            <input type="radio" name="correct_answer" value="{{ $i }}"
                                   {{ $oldAnswer == $i ? 'checked' : '' }}
                                   onchange="updateRadioLabels()">
                            Correct
                        </label>
                        <button type="button" class="remove-option-btn" onclick="removeOption(this)" title="Remove">✕</button>
                    </div>
                @endforeach
            </div>

            <button type="button" onclick="addOption()" id="add-option-btn"
                    class="btn btn-outline btn-sm" style="margin-top:.5rem">
                + Add Option
            </button>

        </div>
    </div>

    {{-- ── Card 3: Scoring & Status ─────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Scoring & Status</h2></div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label>Marks <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="marks" class="form-control"
                           value="{{ old('marks', $isEdit ? $question->marks : '1') }}"
                           min="0.25" max="100" step="0.25" required>
                    @error('marks')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Difficulty Level</label>
                    <select name="test_level_id" class="form-control">
                        <option value="">Select difficulty…</option>
                        @foreach($testLevels as $tl)
                            <option value="{{ $tl->id }}"
                                {{ old('test_level_id', $isEdit ? $question->test_level_id : '') == $tl->id ? 'selected' : '' }}>
                                {{ $tl->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('test_level_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div style="display:flex;gap:1rem;margin-top:.5rem">
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.84rem">
                            <input type="radio" name="is_active" value="1"
                                   {{ old('is_active', $isEdit ? (int)$question->is_active : 1) == 1 ? 'checked' : '' }}
                                   style="accent-color:var(--accent)">
                            <span style="color:var(--text)">Active</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.84rem">
                            <input type="radio" name="is_active" value="0"
                                   {{ old('is_active', $isEdit ? (int)$question->is_active : 1) == 0 ? 'checked' : '' }}
                                   style="accent-color:var(--accent)">
                            <span style="color:var(--text)">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Buttons ───────────────────────────────────────────────────────── --}}
    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.questions.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Save Changes' : 'Add to Question Bank' }}
        </button>
    </div>

</form>

@push('scripts')
<script>
const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

function getRows() {
    return document.querySelectorAll('#options-container .option-row');
}

function rebuildIndexes() {
    const rows = getRows();
    rows.forEach((row, i) => {
        row.dataset.index = i;
        row.querySelector('.option-letter').textContent = letters[i];
        const input = row.querySelector('input[type="text"]');
        input.placeholder = `Option ${letters[i]}…`;
        const radio = row.querySelector('input[type="radio"]');
        radio.value = i;
    });
    updateAddBtn();
}

function updateRadioLabels() {
    getRows().forEach(row => {
        const radio = row.querySelector('input[type="radio"]');
        const label = row.querySelector('.option-radio-wrap');
        label.classList.toggle('selected', radio.checked);
    });
}

function addOption() {
    const rows = getRows();
    if (rows.length >= 6) return;
    const i = rows.length;

    const div = document.createElement('div');
    div.className = 'option-row';
    div.dataset.index = i;
    div.innerHTML = `
        <div class="option-letter">${letters[i]}</div>
        <input type="text" name="options[]" class="form-control"
               placeholder="Option ${letters[i]}…" maxlength="500">
        <label class="option-radio-wrap">
            <input type="radio" name="correct_answer" value="${i}" onchange="updateRadioLabels()">
            Correct
        </label>
        <button type="button" class="remove-option-btn" onclick="removeOption(this)" title="Remove">✕</button>
    `;
    document.getElementById('options-container').appendChild(div);
    updateAddBtn();
    div.querySelector('input[type="text"]').focus();
}

function removeOption(btn) {
    const rows = getRows();
    if (rows.length <= 2) {
        alert('A question must have at least 2 options.');
        return;
    }
    const row = btn.closest('.option-row');

    // If the removed option was the correct answer, reset to first option
    const radio = row.querySelector('input[type="radio"]');
    if (radio.checked) {
        row.remove();
        rebuildIndexes();
        const firstRadio = document.querySelector('#options-container input[type="radio"]');
        if (firstRadio) { firstRadio.checked = true; updateRadioLabels(); }
    } else {
        row.remove();
        rebuildIndexes();
    }
}

function updateAddBtn() {
    const btn = document.getElementById('add-option-btn');
    if (btn) btn.disabled = getRows().length >= 6;
}

// Init
updateRadioLabels();
updateAddBtn();
</script>
@endpush
