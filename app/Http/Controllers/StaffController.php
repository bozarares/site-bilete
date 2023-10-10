<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Event;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function create(Event $event)
    {
        // Get the auth user and store in $user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if the user is an organiser for the event
        $isOrganiser = $user->isOrganising($event);

        // Render the create staff form
        return Inertia::render('Staff/Create', [
            'event_id' => $event->event_id,
            'is_organiser' => $isOrganiser,
        ]);
    }

    public function store(StoreStaffRequest $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate the request data
        $staffData = $request->validated();

        // If the user is not an organiser for the event, set can_edit to 0 by default
        if (!$user->isOrganising($event)) {
            $staffData['can_edit'] = 0;
        }

        // Set the event_id to the unhashed id
        $staffData['event_id'] = $event->id;

        // Create the staff member in the database
        Staff::create($staffData);

        // Redirect to the event page with a success message
        return redirect()
            ->route('events.show', ['event' => $event->event_id])
            ->with(
                'message',
                json_encode([
                    'title' => 'Staff notification',
                    'content' => 'Staff was created successfully',
                    'status' => 'success',
                ])
            );
    }

    public function show(Staff $staff)
    {
        // Not implemented
    }

    public function edit(Request $request, Event $event, Staff $staff)
    {
        // Get the auth user and store in $user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if the user is an organiser for the event
        $isOrganiser = $user->isOrganising($event);

        // Render the edit staff form
        return Inertia::render('Staff/Edit', [
            'event_id' => $event->event_id,
            'staff' => StaffResource::make($staff),
            'is_organiser' => $isOrganiser,
        ]);
    }

    public function update(
        UpdateStaffRequest $request,
        Event $event,
        Staff $staff
    ) {
        // Get the auth user and store in $user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate the request data
        $staffData = $request->validated();

        // If the user is not an organiser for the event and the staff member cannot be edited, set can_edit to 0
        if (!$user->isOrganising($event) && $staff->can_edit === 0) {
            $staffData['can_edit'] = 0;
        }

        // Update the staff member in the database
        $staff->update($staffData);

        // Redirect to the event page with a success message
        return redirect()
            ->route('events.show', ['event' => $event->event_id])
            ->with(
                'message',
                json_encode([
                    'title' => 'Staff notification',
                    'content' => 'Staff was edited successfully',
                    'status' => 'success',
                ])
            );
    }

    public function destroy(Request $request, Event $event, Staff $staff)
    {
        // Get the auth user and store in $user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If the user tries to delete themselves, return an error
        if ($user->uuid == $staff->user_uuid) {
            return response()->json(
                ['message' => 'You cannot delete yourself!'],
                403
            );
        }

        // Delete the staff member
        $staff->delete();

        // Return a success message
        return response()->json([
            'message' => 'Staff was removed successfully',
        ]);
    }
}
