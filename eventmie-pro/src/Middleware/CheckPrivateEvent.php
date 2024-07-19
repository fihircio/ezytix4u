<?php

namespace Classiebit\Eventmie\Middleware;

use Closure;
use Illuminate\Http\Request;
use Classiebit\Eventmie\Models\Event;
use Illuminate\Support\Facades\Session;

class CheckPrivateEvent
{
    public function handle(Request $request, Closure $next)
    {
        $event = $request->route('event');

        if ($event->is_private && !$this->passwordIsVerified($event)) {

            return redirect()->route('eventmie.private_eventform_password', ['event' => $event->slug]);
        }

        return $next($request);
    }

    private function passwordIsVerified($event)
    {
        $sessionPassword = Session::get('event_password_' . $event->id);
        return $sessionPassword && $sessionPassword === $event->event_password;
    }
}
