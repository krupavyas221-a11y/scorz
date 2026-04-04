<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // One PIN per user
    public function pin(): HasOne
    {
        return $this->hasOne(UserPin::class);
    }

    // Global roles — used by Super Admin (no school context)
    public function globalRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('created_at');
    }

    // School-scoped roles — used by School Admin and Teacher
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'user_school_roles')
                    ->using(UserSchoolRole::class)
                    ->withPivot(['role_id', 'is_active', 'assigned_at'])
                    ->withTimestamps();
    }

    // Check if user has a global role (e.g. Super Admin)
    public function isSuperAdmin(): bool
    {
        return $this->globalRoles()->where('slug', 'super_admin')->exists();
    }

    // Check if user holds a specific role at a specific school
    public function hasRoleInSchool(string $roleSlug, int $schoolId): bool
    {
        return UserSchoolRole::where('user_id', $this->id)
                             ->where('school_id', $schoolId)
                             ->where('is_active', true)
                             ->whereHas('role', fn ($q) => $q->where('slug', $roleSlug))
                             ->exists();
    }

    // Get all roles a user holds at a specific school
    public function rolesInSchool(int $schoolId): \Illuminate\Support\Collection
    {
        return UserSchoolRole::with('role')
                             ->where('user_id', $this->id)
                             ->where('school_id', $schoolId)
                             ->where('is_active', true)
                             ->get()
                             ->pluck('role');
    }
}
