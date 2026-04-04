<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\Season;

class SeasonController extends BaseMasterController
{
    protected function modelClass(): string  { return Season::class; }
    protected function table(): string       { return 'seasons'; }
    protected function label(): string       { return 'Seasons'; }
    protected function singular(): string    { return 'Season'; }
    protected function routePrefix(): string { return 'superadmin.seasons'; }
}
