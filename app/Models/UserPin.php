<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPin extends Model
{
    protected $fillable = [
        'user_id',
        'pin',
    ];

    protected $hidden = ['pin'];

    protected function casts(): array
    {
        return [
            'pin' => 'hashed',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
