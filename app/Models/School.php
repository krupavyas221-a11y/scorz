<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pupils(): HasMany
    {
        return $this->hasMany(Pupil::class);
    }

    public function userSchoolRoles(): HasMany
    {
        return $this->hasMany(UserSchoolRole::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_school_roles')
                    ->using(UserSchoolRole::class)
                    ->withPivot(['role_id', 'is_active', 'assigned_at'])
                    ->withTimestamps();
    }
}
