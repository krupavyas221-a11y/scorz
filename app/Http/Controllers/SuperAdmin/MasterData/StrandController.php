<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\Strand;

class StrandController extends BaseMasterController
{
    protected function modelClass(): string  { return Strand::class; }
    protected function table(): string       { return 'strands'; }
    protected function label(): string       { return 'Strands'; }
    protected function singular(): string    { return 'Strand'; }
    protected function routePrefix(): string { return 'superadmin.strands'; }
}
