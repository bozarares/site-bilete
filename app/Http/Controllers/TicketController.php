<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        // Get the tickets for the current user
        /** @var \App\Models\User $user */
        $tickets = Auth::user()->tickets;

        // Render the Tickets/Index page with the ticket data
        return Inertia::render('Tickets/Index', [
            'tickets' => TicketResource::collection($tickets),
        ]);
    }

    public function showValidateTicket(Event $event, Ticket $ticket)
    {
        // Render the Tickets/ShowValidate page with the event_id and ticket data
        return Inertia::render('Tickets/ShowValidate', [
            'event_id' => $event->event_id,
            'ticket' => TicketResource::make($ticket),
        ]);
    }
    // Validate the specified ticket
    public function validateTicket(
        Request $request,
        Event $event,
        Ticket $ticket
    ) {
        // Check if the ticket has already been validated
        if ($ticket->validated === 1) {
            return redirect()
                ->route('event.validate', ['event' => $event->event_id])
                ->with(
                    'message',
                    json_encode([
                        'title' => 'Ticket notification',
                        'content' => 'Ticket has already been validated',
                        'status' => 'error',
                    ])
                );
        }

        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Update the ticket validation status and save to the database
        $ticket->validated = 1;
        $ticket->validated_by_uuid = $user->uuid;
        $ticket->save();

        // Redirect to the event validation page
        return redirect()
            ->route('event.validate', [
                'event' => $event->event_id,
            ])
            ->with(
                'message',
                json_encode([
                    'title' => 'Ticket notification',
                    'content' => 'Ticket was validated successfully',
                    'status' => 'success',
                ])
            );
    }

    // Display the specified ticket
    public function show(Ticket $ticket)
    {
        // Render the Tickets/Show page with the ticket data
        return Inertia::render('Tickets/Show', [
            'ticket' => TicketResource::make($ticket),
        ]);
    }

    // Show the form for editing the specified ticket
    public function edit(Ticket $ticket)
    {
        // Check if the ticket has already been validated
        if ($ticket->validated === 1) {
            return redirect()->route('tickets.show', [
                'ticket' => $ticket->uuid,
            ]);
        }

        // Render the Tickets/Edit page with the ticket data
        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
        ]);
    }

    // Update the specified ticket in storage
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // Update the ticket with the validated data
        $ticket->update($request->validated());

        // Check if the ticket has already been validated
        if ($ticket->validated === 1) {
            return redirect()->route('tickets.show', [
                'ticket' => $ticket->uuid,
            ]);
        }

        // Redirect to the ticket show page with the updated ticket data
        return redirect()
            ->route('tickets.show', ['ticket' => $ticket->uuid])
            ->with(
                'message',
                json_encode([
                    'title' => 'Ticket Notification',
                    'content' => 'Ticket was updated successfully',
                    'status' => 'success',
                ])
            );
    }
}
