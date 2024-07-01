<?php

namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Classiebit\Eventmie\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Redirect;

class PrivateEventController extends Controller
{

        /**
     * Create a new controller instance.
     * 
     *
     * @return void
     */
    public function __construct()
    {
        // language change
        $this->middleware('common');
        
        $this->middleware('organiser')->except('verify_event_password');
        
        $this->event    = new Event;

    }


    /**
     *  save event password for private event
     */
    public function save_password(Request $request)
    {
        $request->validate([
            'event_id'                 => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
            'event_password'           => 'string|max:512|nullable',
            'is_private'               => 'nullable'
        ]);
 
        $params = [
            'event_password' =>  $request->event_password,
            'is_private'      => !empty($request->is_private) ? 1 : 0,
        ];

        if(!empty($request->event_password))
        {
            $params['is_private'] = 1;
        }

        $this->event->save_event($params, $request->event_id);

        return response()->json(['status' => true]);
    }

    /**
     *  verifiy event password and set password in user session
     */

     public function verify_event_password(Request $request)
    {
        $request->validate([
            'event_slug' => 'required|string',
            'event_password' => 'required|max:512',
        ]);

        $event = Event::where('slug', $request->event_slug)->firstOrFail();
        
        // Debug information
        /*
        \Log::info('Event Password Verification', [
            'input_password' => $request->event_password,
            'stored_password' => $event->event_password,
            'password_match' => $request->event_password === $event->event_password,
            'event_id' => $event->id,
            'event_slug' => $event->slug,
        ]);
        */

        if ($request->event_password === $event->event_password) {
            // Set event password in session
            \Session::put('event_password_'.$event->id, $event->event_password);
            //dd('Password verified', $event->slug);
            // Redirect to the event page
            return redirect()->action('\Classiebit\Eventmie\Http\Controllers\EventsController@show', [$event->slug]);
        }

        // If password doesn't match, show error
        $msg = __('eventmie-pro::em.invalid').' '.__('eventmie-pro::em.password');
        return redirect()->back()->withErrors(['event_password' => $msg]);
    }
     

}
