<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'code', 'address', 'is_active',
        'school_type', 'region', 'gender', 'phone', 'fax',
        'principal_name', 'email', 'website',
        'teacher_council_number', 'school_years',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'school_years' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
            if (empty($school->code)) {
                $school->code = strtoupper(Str::random(6));
            }
        });
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

    // Get the primary school admin user
    public function admin(): ?User
    {
        return $this->users()
                    ->wherePivot('role_id', function ($q) {
                        $q->select('id')->from('roles')->where('slug', 'school_admin');
                    })
                    ->wherePivot('is_active', true)
                    ->first();
    }

    public function schoolAdmins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_school_roles')
                    ->using(UserSchoolRole::class)
                    ->wherePivot('role_id', Role::where('slug', 'school_admin')->value('id'))
                    ->withPivot(['is_active', 'assigned_at'])
                    ->withTimestamps();
    }

    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(SchoolYear::class, 'school_school_year')->withTimestamps();
    }
}
