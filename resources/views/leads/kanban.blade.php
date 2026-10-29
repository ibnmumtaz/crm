@extends('layouts.app')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Pipeline Kanban</h2>
            <a href="{{ route('leads.index') }}" class="bg-figma-soft border border-figma-hairline text-figma-ink px-4 py-2 rounded-full font-bold text-sm">List View</a>
        </div>

        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach($pipeline->stages as $stage)
                <div class="flex-shrink-0 w-72 bg-figma-soft border border-figma-hairline rounded-md p-3 kanban-column" data-stage-id="{{ $stage->id }}">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-figma-ink">{{ $stage->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-gray-600 font-bold border border-figma-hairline count">{{ isset($leads[$stage->id]) ? $leads[$stage->id]->count() : 0 }}</span>
                    </div>
                    <div class="space-y-2 min-h-[100px] kanban-dropzone">
                        @if(isset($leads[$stage->id]))
                            @foreach($leads[$stage->id] as $lead)
                                <div class="bg-figma-soft border border-figma-hairline rounded-md p-3 cursor-move kanban-card" draggable="true" data-lead-id="{{ $lead->id }}">
                                    <div class="font-bold text-figma-ink text-sm">{{ $lead->name }}</div>
                                    <div class="text-xs text-gray-600">{{ $lead->company }}</div>
                                    <div class="text-xs text-figma-ink font-bold mt-1">${{ number_format($lead->value, 2) }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        (function() {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            let dragged = null;
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.addEventListener('dragstart', e => { dragged = card; card.classList.add('opacity-50'); });
                card.addEventListener('dragend', e => { card.classList.remove('opacity-50'); });
            });
            document.querySelectorAll('.kanban-dropzone').forEach(zone => {
                zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('bg-figma-soft'); });
                zone.addEventListener('dragleave', e => { zone.classList.remove('bg-figma-soft'); });
                zone.addEventListener('drop', e => {
                    e.preventDefault();
                    zone.classList.remove('bg-figma-soft');
                    if (!dragged) return;
                    const stageId = zone.closest('.kanban-column').dataset.stageId;
                    const leadId = dragged.dataset.leadId;
                    fetch('/leads/' + leadId + '/stage', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify({ stage_id: stageId })
                    }).then(r => {
                        if (r.ok) { zone.appendChild(dragged); }
                        else { alert('Move failed'); }
                    });
                });
            });
        })();
    </script>
@endsection
