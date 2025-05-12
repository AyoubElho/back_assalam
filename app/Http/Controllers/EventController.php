<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'location' => 'nullable|string|max:255',
        ]);

        $event = Event::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json($event, 201);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start' => 'sometimes|date',
            'end' => 'sometimes|date|after_or_equal:start',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully.']);
    }

    public function getAll()
    {
        $events = Event::with('creator')->orderBy('created_at', 'desc')->get();
        return response()->json($events);
    }

    public function show(Event $event)
    {
        return response()->json($event);
    }
}
