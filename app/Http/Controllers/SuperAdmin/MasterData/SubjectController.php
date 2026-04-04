<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\Subject;

class SubjectController extends BaseMasterController
{
    protected function modelClass(): string  { return Subject::class; }
    protected function table(): string       { return 'subjects'; }
    protected function label(): string       { return 'Subjects'; }
    protected function singular(): string    { return 'Subject'; }
    protected function routePrefix(): string { return 'superadmin.subjects'; }
}
