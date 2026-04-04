<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\TestLevel;

class TestLevelController extends BaseMasterController
{
    protected function modelClass(): string  { return TestLevel::class; }
    protected function table(): string       { return 'test_levels'; }
    protected function label(): string       { return 'Test Levels'; }
    protected function singular(): string    { return 'Test Level'; }
    protected function routePrefix(): string { return 'superadmin.test-levels'; }
}
