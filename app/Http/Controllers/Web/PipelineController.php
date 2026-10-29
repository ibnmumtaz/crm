<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $pipelines = Pipeline::with('stages')->get();

        return view('pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        $this->ensureManage();

        return view('pipelines.create');
    }

    public function store(Request $request)
    {
        $this->ensureManage();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        Pipeline::create($validated);

        return redirect()->route('pipelines.index')->with('success', 'Pipeline created successfully.');
    }

    public function show(Pipeline $pipeline)
    {
        $pipeline->load('stages.leads');

        return view('pipelines.show', compact('pipeline'));
    }

    public function edit(Pipeline $pipeline)
    {
        $this->ensureManage();

        return view('pipelines.edit', compact('pipeline'));
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $this->ensureManage();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $pipeline->update($validated);

        return redirect()->route('pipelines.index')->with('success', 'Pipeline updated successfully.');
    }

    public function destroy(Pipeline $pipeline)
    {
        $this->ensureManage();

        if ($pipeline->leads()->exists()) {
            return back()->with('error', 'Cannot delete a pipeline that still has leads.');
        }

        $pipeline->stages()->delete();
        $pipeline->delete();

        return redirect()->route('pipelines.index')->with('success', 'Pipeline deleted successfully.');
    }

    // Stage management helper routes are not exposed; keep stage deletion guarded.
    public function destroyStage(PipelineStage $stage)
    {
        $this->ensureManage();

        if ($stage->leads()->exists()) {
            return back()->with('error', 'Cannot delete a stage that still has leads.');
        }

        $stage->delete();

        return back()->with('success', 'Stage deleted successfully.');
    }

    private function ensureManage(): void
    {
        if (! auth()->user()->can('pipeline.manage')) {
            abort(403);
        }
    }
}
