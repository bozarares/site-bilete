<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketCategoryRequest;
use App\Http\Requests\UpdateTicketCategoryRequest;
use App\Http\Resources\TicketCategoryResource;
use App\Models\Event;
use App\Models\TicketCategory;
use Inertia\Inertia;

class TicketCategoryController extends Controller
{
    public function actions(Event $event, TicketCategory $ticketCategory)
    {
        // Render the TicketCategory/Actions page with the event_id and ticket category data
        return Inertia::render('TicketCategory/Actions', [
            'event_id' => $event->event_id,
            'ticket_category' => TicketCategoryResource::make($ticketCategory),
        ]);
    }

    public function toggle(Event $event, TicketCategory $ticketCategory)
    {
        try {
            // Reverse the paused status of the ticket category and save to the database
            $ticketCategory->paused = !$ticketCategory->paused;
            $ticketCategory->save();

            // Set the success message based on the new paused status
            if ($ticketCategory->paused == 0) {
                $message = 'Ticket category was unpaused successfully';
            } else {
                $message = 'Ticket category was paused successfully';
            }

            // Return a success message
            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            // Return an error message
            return response()->json(
                ['message' => 'There was a problem updating the status.'],
                403
            );
        }
    }

    // Show the form for creating a new ticket category
    public function create(Event $event)
    {
        // Render the TicketCategory/Create page with the event_id
        return Inertia::render('TicketCategory/Create', [
            'event_id' => $event->event_id,
        ]);
    }

    // Store a newly created ticket category in storage
    public function store(StoreTicketCategoryRequest $request, Event $event)
    {
        // Validate the request data
        $validated_request = $request->validated();

        // Append the event_id to the validated data
        $validated_request['event_id'] = $event->id;

        // Create and store the new TicketCategory
        TicketCategory::create($validated_request);

        // Redirect to the event.show page
        return redirect()->route('events.show', ['event' => $event->event_id]);
    }

    public function show(TicketCategory $ticketCategory)
    {
        // Not implemented
    }

    // Show the form for editing the specified ticket category
    public function edit(Event $event, TicketCategory $ticketCategory)
    {
        // Render the TicketCategory/Edit page with the event_id and ticket category data
        return Inertia::render('TicketCategory/Edit', [
            'event_id' => $event->event_id,
            'ticket_category' => TicketCategoryResource::make($ticketCategory),
        ]);
    }

    // Update the specified ticket category in storage
    public function update(
        UpdateTicketCategoryRequest $request,
        Event $event,
        TicketCategory $ticketCategory
    ) {
        // Update the ticket category with the validated data
        $ticketCategory->update($request->validated());

        // Redirect to the ticket_category.actions page with a success message
        return redirect()
            ->route('ticket_category.actions', [
                'event' => $event->event_id,
                'ticket_category' => $ticketCategory->ticket_category_id,
            ])
            ->with(
                'message',
                json_encode([
                    'title' => 'Ticket Category Notification',
                    'content' => 'Ticket Category was edited successfully',
                    'status' => 'success',
                ])
            );
    }

    // Remove the specified ticket category from storage
    public function destroy(Event $event, TicketCategory $ticketCategory)
    {
        // Check if the ticket category has any bought tickets
        if ($ticketCategory->getBoughtTickets() == 0) {
            // Delete the ticket category from the database
            $ticketCategory->delete();
        }

        // Redirect to the event.show page with a success message
        return redirect()
            ->route('events.show', ['event' => $event->event_id])
            ->with(
                'message',
                json_encode([
                    'title' => 'Ticket Category Notification',
                    'content' => 'Ticket Category was deleted successfully',
                    'status' => 'success',
                ])
            );
    }
}
