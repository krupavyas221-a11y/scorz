<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'question_id',
        'subject_id',
        'class_id',
        'strand_id',
        'skill_category_id',
        'test_type_id',
        'season_id',
        'test_level_id',
        'duration',
        'marking_scheme',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'marks',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options'        => 'array',
            'correct_answer' => 'integer',
            'marks'          => 'decimal:2',
            'is_active'      => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Question $question) {
            if (empty($question->question_id)) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;

                // Retry if collision (race condition guard)
                $id = 'Q' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                while (static::where('question_id', $id)->exists()) {
                    $count++;
                    $id = 'Q' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                }
                $question->question_id = $id;
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    public function skillCategory(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class);
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function testLevel(): BelongsTo
    {
        return $this->belongsTo(TestLevel::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getCorrectOptionTextAttribute(): ?string
    {
        $opts = $this->options ?? [];
        return $opts[$this->correct_answer] ?? null;
    }

    public function getCorrectOptionLetterAttribute(): string
    {
        return chr(65 + $this->correct_answer); // 0→A, 1→B, ...
    }
}
