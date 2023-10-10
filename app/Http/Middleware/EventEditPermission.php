<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EventEditPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $event = $request->route('event');
        $staff = $event->staffs->where('user_uuid', $user->uuid)->first();

        // Permit access if the user is either the event organiser or a staff member with can_edit permission
        if (!$user->isOrganising($event) && (!$staff || !$staff->can_edit)) {
            return redirect()->route('no_access', [], 303);
        }

        return $next($request);
    }
}
