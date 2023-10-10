<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function indexEvents(Request $request)
    {
        $events = Auth::getUser()->events;
        $info = [
            'title' => 'My Events',
            'description' => 'My events.',
        ];
        return Inertia::render('Events/Index', [
            'events' => $events,
            'info' => $info,
        ]);
    }

    public function indexStaffedEvents()
    {
        $staffs = Auth::getUser()->staffs;
        $events = $staffs->map->event;

        return Inertia::render('Events/Index', ['events' => $events]);
    }
}
