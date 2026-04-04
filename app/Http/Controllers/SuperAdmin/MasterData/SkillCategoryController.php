<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Models\SkillCategory;

class SkillCategoryController extends BaseMasterController
{
    protected function modelClass(): string  { return SkillCategory::class; }
    protected function table(): string       { return 'skill_categories'; }
    protected function label(): string       { return 'Skill Categories'; }
    protected function singular(): string    { return 'Skill Category'; }
    protected function routePrefix(): string { return 'superadmin.skill-categories'; }
}
