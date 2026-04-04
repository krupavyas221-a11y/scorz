<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\TestType;

class TestTypeController extends BaseMasterController
{
    protected function modelClass(): string  { return TestType::class; }
    protected function table(): string       { return 'test_types'; }
    protected function label(): string       { return 'Test Types'; }
    protected function singular(): string    { return 'Test Type'; }
    protected function routePrefix(): string { return 'superadmin.test-types'; }
}
