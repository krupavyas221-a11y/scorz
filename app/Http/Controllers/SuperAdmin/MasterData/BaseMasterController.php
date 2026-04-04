<?php

namespace App\Http\Controllers\SuperAdmin\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

abstract class BaseMasterController extends Controller
{
    // ── Each subclass declares its own identity ───────────────────────────────

    /** Eloquent model class string */
    abstract protected function modelClass(): string;

    /** Database table name */
    abstract protected function table(): string;

    /** Plural label, e.g. "Subjects" */
    abstract protected function label(): string;

    /** Singular label, e.g. "Subject" */
    abstract protected function singular(): string;

    /** Named route prefix without trailing dot, e.g. "superadmin.subjects" */
    abstract protected function routePrefix(): string;

    // ── All-types registry (used by views for tab navigation) ─────────────────
    public static function allTypes(): array
    {
        return [
            ['label' => 'Subjects',         'route' => 'superadmin.subjects.index'],
            ['label' => 'Strands',          'route' => 'superadmin.strands.index'],
            ['label' => 'Skill Categories', 'route' => 'superadmin.skill-categories.index'],
            ['label' => 'Test Types',       'route' => 'superadmin.test-types.index'],
            ['label' => 'Seasons',          'route' => 'superadmin.seasons.index'],
            ['label' => 'Test Levels',      'route' => 'superadmin.test-levels.index'],
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function config(): array
    {
        return [
            'label'        => $this->label(),
            'singular'     => $this->singular(),
            'route_prefix' => $this->routePrefix(),
        ];
    }

    private function findEntry(int $id): Model
    {
        $class = $this->modelClass();
        return $class::findOrFail($id);
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $class    = $this->modelClass();
        $config   = $this->config();
        $allTypes = static::allTypes();

        $query = $class::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $entries = $query->orderBy('name')->paginate(20)->withQueryString();

        $editing = null;
        if ($editId = $request->integer('edit')) {
            $editing = $class::find($editId);
        }

        return view('superadmin.master.index', compact(
            'config', 'allTypes', 'entries', 'editing'
        ));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100',
                       Rule::unique($this->table(), 'name')],
        ]);

        $class = $this->modelClass();
        $class::create(['name' => trim($request->name), 'is_active' => true]);

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', "{$this->singular()} \"{$request->name}\" added successfully.");
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, int $entry): RedirectResponse
    {
        $model = $this->findEntry($entry);

        $request->validate([
            'name' => ['required', 'string', 'max:100',
                       Rule::unique($this->table(), 'name')->ignore($model->id)],
        ]);

        $model->update(['name' => trim($request->name)]);

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', "{$this->singular()} updated successfully.");
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(int $entry): RedirectResponse
    {
        $model = $this->findEntry($entry);
        $name  = $model->name;
        $model->delete();

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', "{$this->singular()} \"{$name}\" deleted.");
    }

    // ── TOGGLE STATUS ─────────────────────────────────────────────────────────
    public function toggleStatus(int $entry): RedirectResponse
    {
        $model = $this->findEntry($entry);
        $model->update(['is_active' => ! $model->is_active]);

        $status = $model->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$this->singular()} \"{$model->name}\" {$status}.");
    }

    // ── EXPORT CSV ────────────────────────────────────────────────────────────
    public function exportCsv(): Response
    {
        $class   = $this->modelClass();
        $entries = $class::orderBy('name')->get();

        $csv = "ID,Name,Status,Created At\n";
        foreach ($entries as $row) {
            $csv .= implode(',', [
                $row->id,
                '"' . str_replace('"', '""', $row->name) . '"',
                $row->is_active ? 'Active' : 'Inactive',
                $row->created_at->format('d/m/Y'),
            ]) . "\n";
        }

        $filename = strtolower(str_replace(' ', '_', $this->label()))
                    . '_' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── EXPORT PDF ────────────────────────────────────────────────────────────
    public function exportPdf(): View
    {
        $class   = $this->modelClass();
        $entries = $class::orderBy('name')->get();
        $config  = $this->config();

        return view('superadmin.master.export-pdf', compact('config', 'entries'));
    }
}
