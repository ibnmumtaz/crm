<?php

namespace App\Http\Controllers\Web;

use App\Enums\CommunicationType;
use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\Lead;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CommunicationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Communication::class);

        $user = $request->user();
        $query = Communication::visibleTo($user);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->integer('lead_id'));
        }

        $communications = $query->latest('occurred_at')->paginate(15)->withQueryString();

        return view('communications.index', compact('communications'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Communication::class);

        $leads = Lead::visibleTo($request->user())->get();

        return view('communications.create', compact('leads'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Communication::class);

        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'type' => ['required', new Enum(CommunicationType::class)],
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'outcome' => 'nullable|string|max:255',
            'occurred_at' => 'required|date',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        $this->authorize('view', $lead);

        $validated['user_id'] = $request->user()->id;

        Communication::create($validated);

        return redirect()->route('communications.index')->with('success', 'Communication logged successfully.');
    }

    public function show(Communication $communication)
    {
        $this->authorize('view', $communication);

        $communication->load(['lead', 'user']);

        return view('communications.show', compact('communication'));
    }

    public function edit(Communication $communication)
    {
        $this->authorize('update', $communication);

        $leads = Lead::visibleTo(request()->user())->get();

        return view('communications.edit', compact('communication', 'leads'));
    }

    public function update(Request $request, Communication $communication)
    {
        $this->authorize('update', $communication);

        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'type' => ['required', new Enum(CommunicationType::class)],
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'outcome' => 'nullable|string|max:255',
            'occurred_at' => 'required|date',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        $this->authorize('view', $lead);

        $communication->update($validated);

        return redirect()->route('communications.index')->with('success', 'Communication updated successfully.');
    }

    public function destroy(Communication $communication)
    {
        $this->authorize('delete', $communication);
        $communication->delete();

        return redirect()->route('communications.index')->with('success', 'Communication deleted successfully.');
    }
}
