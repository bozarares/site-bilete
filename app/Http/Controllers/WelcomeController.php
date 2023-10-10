<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventIndexResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $events = Event::query()
            ->when($request->search, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query
                        ->where('title', 'LIKE', '%' . $term . '%')
                        ->orWhere('location', 'LIKE', '%' . $term . '%');
                });
            })
            ->when($request->type, function ($query, $type) {
                $eventTypeId = EventType::where('name', $type)
                    ->pluck('id')
                    ->first();
                $query->where('event_type_id', $eventTypeId);
            })
            ->paginate(10);

        return Inertia::render('Welcome', [
            // 'canLogin' => Route::has('login'),
            // 'canRegister' => Route::has('register'),
            // Converteste colecția paginată într-un resource
            'events' => EventIndexResource::collection($events),
            'links' => [
                'next' => $events->nextPageUrl(),
                'prev' => $events->previousPageUrl(),
                'last' => $events->url($events->lastPage()),
                'first' => $events->url(1),
                'current' => $events->currentPage(),
                'total' => $events->lastPage(),
            ],
        ]);
    }
}
