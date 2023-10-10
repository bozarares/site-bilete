<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventIndexResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function all()
    {
        // Get all events
        $events = Event::select('title', 'location')->get();
        return response()->json($events);
    }
    public function types()
    {
        // Get all event types
        $event_types = EventType::select('name')->get();
        return response()->json($event_types);
    }
    public function validateTicket(Event $event)
    {
        return Inertia::render('Events/ValidateTicket', [
            'event' => $event->event_id,
        ]);
    }
    public function index(Request $request)
    {
        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the show parameter
        $show = $request->get('show');
        $events = [];

        // If the show parameter is set to "myevents", get the user's events
        if ($show === 'myevents') {
            $events = $user->events;
        }
        // If the show parameter is set to "mystaffed", get the user's staffed events
        elseif ($show === 'mystaffed') {
            $staffs = $user->staffs;
            $events = $staffs->map->event;
        }
        // If the show parameter doesn't exist, get all events
        else {
            $events = Event::all();
        }

        return Inertia::render('Events/Index', [
            'events' => EventIndexResource::collection($events),
        ]);
    }

    public function show(Event $event)
    {
        // Create a new event resource and render the show page
        $eventResource = new EventResource($event);
        return Inertia::render('Events/Show', ['event' => $eventResource]);
    }

    public function edit(Event $event)
    {
        // Get all event types
        $event_types = EventType::all();

        // Render the edit page with the event and event types
        return Inertia::render('Events/Edit', [
            'event' => [
                'id' => $event->event_id,
                'title' => $event->title,
                'event_type' => $event->eventType,
                'location' => $event->location,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
            ],
            'types' => $event_types,
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        // Update the event and redirect to the show page
        $event->update($request->validated());

        return redirect()
            ->route('events.show', ['event' => $event->event_id])
            ->with(
                'message',
                json_encode([
                    'title' => 'Event notification',
                    'content' => 'Event was edited successfully',
                    'status' => 'success',
                ])
            );
    }
}
