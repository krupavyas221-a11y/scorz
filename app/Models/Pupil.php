<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pupil extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'school_id',
        'pupil_id',
        'name',
        'date_of_birth',
        'year_group',
        'pin',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = ['pin'];

    protected function casts(): array
    {
        return [
            'pin'            => 'hashed',
            'is_active'      => 'boolean',
            'date_of_birth'  => 'date',
            'last_login_at'  => 'datetime',
        ];
    }

    // Required by Laravel auth: the field used to identify the user
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    // The field used for "remember me" token (not used for pupils but required by contract)
    public function getAuthPassword(): string
    {
        return $this->pin;
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
