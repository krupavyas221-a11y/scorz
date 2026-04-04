<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Pupil extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'school_id',
        'pupil_id',
        'name',
        'first_name',
        'last_name',
        'date_of_birth',
        'year_group',
        'class_name',
        'teacher_id',
        'include_in_averages',
        'sen',
        'pin',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = ['pin'];

    protected function casts(): array
    {
        return [
            'pin'                 => 'hashed',
            'is_active'           => 'boolean',
            'include_in_averages' => 'boolean',
            'date_of_birth'       => 'date',
            'last_login_at'       => 'datetime',
        ];
    }

    // ------------------------------------------------------------------ Boot
    protected static function booted(): void
    {
        static::creating(function (Pupil $pupil) {
            // Auto-generate unique Pupil ID if not set
            if (empty($pupil->pupil_id)) {
                do {
                    $id = 'P' . now()->format('Y') . strtoupper(Str::random(4));
                } while (static::where('pupil_id', $id)->exists());

                $pupil->pupil_id = $id;
            }

            // Sync name from first/last
            if (empty($pupil->name) && ($pupil->first_name || $pupil->last_name)) {
                $pupil->name = trim("{$pupil->first_name} {$pupil->last_name}");
            }
        });

        static::updating(function (Pupil $pupil) {
            if ($pupil->isDirty(['first_name', 'last_name'])) {
                $pupil->name = trim("{$pupil->first_name} {$pupil->last_name}");
            }
        });
    }

    // ------------------------------------------------------------------ Accessors
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_birth
                ? $this->date_of_birth->age
                : null,
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    public function getSenLabelAttribute(): string
    {
        return match ($this->sen) {
            'sen_support' => 'SEN Support',
            'ehc_plan'    => 'EHC Plan',
            default       => 'None',
        };
    }

    // ------------------------------------------------------------------ Auth contract
    public function getAuthIdentifierName(): string { return 'id'; }
    public function getAuthPassword(): string       { return $this->pin; }

    // ------------------------------------------------------------------ Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
