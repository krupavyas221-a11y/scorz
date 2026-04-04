<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestLevel extends Model
{
    protected $table = 'test_levels';

    protected $fillable = ['name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
