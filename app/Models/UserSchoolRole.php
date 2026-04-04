<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSchoolRole extends Pivot
{
    protected $table = 'user_school_roles';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'school_id',
        'role_id',
        'is_active',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
