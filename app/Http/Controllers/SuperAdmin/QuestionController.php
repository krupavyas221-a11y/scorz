<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Season;
use App\Models\SkillCategory;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\TestLevel;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    // ── Shared lookup data ────────────────────────────────────────────────────
    private function lookups(): array
    {
        return [
            'subjects'        => Subject::where('is_active', true)->orderBy('name')->get(),
            'classes'         => SchoolClass::where('is_active', true)->orderBy('name')->get(),
            'strands'         => Strand::where('is_active', true)->orderBy('name')->get(),
            'skillCategories' => SkillCategory::where('is_active', true)->orderBy('name')->get(),
            'testTypes'       => TestType::where('is_active', true)->orderBy('name')->get(),
            'seasons'         => Season::where('is_active', true)->orderBy('name')->get(),
            'testLevels'      => TestLevel::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Question::with([
            'subject', 'schoolClass', 'strand',
            'skillCategory', 'testType', 'testLevel',
        ]);

        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('question_text', 'like', "%{$search}%")
                ->orWhere('question_id', 'like', "%{$search}%")
            );
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }
        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->input('strand_id'));
        }
        if ($request->filled('test_type_id')) {
            $query->where('test_type_id', $request->input('test_type_id'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $questions = $query->latest()->paginate(20)->withQueryString();

        $filterData = [
            'subjects'  => Subject::where('is_active', true)->orderBy('name')->get(),
            'classes'   => SchoolClass::where('is_active', true)->orderBy('name')->get(),
            'strands'   => Strand::where('is_active', true)->orderBy('name')->get(),
            'testTypes' => TestType::where('is_active', true)->orderBy('name')->get(),
        ];

        return view('superadmin.questions.index', compact('questions', 'filterData'));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create(): View
    {
        return view('superadmin.questions.create', $this->lookups());
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        Question::create($data);

        return redirect()
            ->route('superadmin.questions.index')
            ->with('success', 'Question added to the bank successfully.');
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(Question $question): View
    {
        return view('superadmin.questions.edit',
            array_merge(['question' => $question], $this->lookups())
        );
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, Question $question): RedirectResponse
    {
        $data = $this->validateQuestion($request, $question);

        $question->update($data);

        return redirect()
            ->route('superadmin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(Question $question): RedirectResponse
    {
        $qid = $question->question_id;
        $question->delete();

        return redirect()
            ->route('superadmin.questions.index')
            ->with('success', "Question {$qid} deleted.");
    }

    // ── TOGGLE STATUS ─────────────────────────────────────────────────────────
    public function toggleStatus(Question $question): RedirectResponse
    {
        $question->update(['is_active' => ! $question->is_active]);
        $status = $question->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Question {$question->question_id} {$status}.");
    }

    // ── VALIDATION ────────────────────────────────────────────────────────────
    private function validateQuestion(Request $request, ?Question $question = null): array
    {
        $validated = $request->validate([
            'subject_id'        => ['required', 'exists:subjects,id'],
            'class_id'          => ['nullable', 'exists:classes,id'],
            'strand_id'         => ['nullable', 'exists:strands,id'],
            'skill_category_id' => ['nullable', 'exists:skill_categories,id'],
            'test_type_id'      => ['nullable', 'exists:test_types,id'],
            'season_id'         => ['nullable', 'exists:seasons,id'],
            'test_level_id'     => ['nullable', 'exists:test_levels,id'],
            'duration'          => ['nullable', 'integer', 'min:1', 'max:600'],
            'marking_scheme'    => ['nullable', 'string', 'max:255'],
            'question_text'     => ['required', 'string', 'max:2000'],
            'options'           => ['required', 'array', 'min:2', 'max:6'],
            'options.*'         => ['required', 'string', 'max:500'],
            'correct_answer'    => ['required', 'integer', 'min:0'],
            'marks'             => ['required', 'numeric', 'min:0.25', 'max:100'],
        ]);

        // Ensure correct_answer index is within bounds of provided options
        $optionCount = count($validated['options']);
        if ($validated['correct_answer'] >= $optionCount) {
            abort(422, 'Correct answer index is out of range.');
        }

        $validated['question_type'] = 'mcq';

        return $validated;
    }
}
