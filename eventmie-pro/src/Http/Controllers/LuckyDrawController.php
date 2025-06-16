<?php

namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Facades\Classiebit\Eventmie\Eventmie;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;
use Classiebit\Eventmie\Models\Booking;
use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Models\Event;

class LuckyDrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('common');
    
        $this->middleware(['organiser']);
        $this->event        = new Event;
        $this->user         = new User;
        $this->organiser_id = null;
    }


    public function index($view = 'eventmie::luckydraw.index', $extra = [])
    {
        // get prifex from eventmie config
        $path = false;
        if(!empty(config('eventmie.route.prefix')))
            $path = config('eventmie.route.prefix');

        // admin can't see organiser bookings
        if(Auth::user()->hasRole('admin'))
        {
            return redirect()->route('voyager.events.index');   
        }
        
        return Eventmie::view($view, compact('path', 'extra'));
    }

    public function participants2(Request $request)
    {
        // Validate the request (ensure event_id is provided)
        $request->validate([
            //'event_id' => 'required|integer|exists:events,id',
        ]);

        $event_id = $request->event_id;
        $organiser_id = Auth::id(); // Get the organizer's ID

        // Fetch the paid users for the selected event
        $paidUsers = Booking::where([
            'event_id' => $event_id,
            'is_paid' => 1,
        ])
        ->distinct('customer_id')
        ->pluck('customer_id')
        ->toArray();

        // Implement the lucky draw logic (e.g., use rand() to select a random user)
        $winnerId = $paidUsers ? $paidUsers[array_rand($paidUsers)] : null;

        if ($winnerId) {
            // Fetch the winner's details from the User model
            $winner = User::find($winnerId);

            // Display or process the winner's information
            // ...

            // You can send a notification to the winner, update event status, etc.

        } else {
            // Handle the case where no paid users were found
            // ...
        }

        // Return a response (e.g., a success message, the winner's details, etc.)
        return response()->json(['status' => true, 'winnerId' => $winnerId ?? null]);
    }

    public function participants(Request $request)
    {
        if(Auth::user()->hasRole('admin'))
        {
            return redirect()->route('voyager.events.index');   
        }

        $params   = [
            'organiser_id' => Auth::id(),
        ];

        $myevents    = $this->event->get_my_events($params);

        if(empty($myevents))
            return error(__('eventmie-pro::em.event').' '.__('eventmie-pro::em.not_found'), Response::HTTP_BAD_REQUEST );
        
        return response([
            'myevents'=> $myevents->jsonSerialize(),
        ], Response::HTTP_OK);

    }

    /**
     *  my all  event for particular organiser
     */

    public function get_all_myevents()
    {
        if(Auth::user()->hasRole('admin'))
        {
            return redirect()->route('voyager.events.index');   
        }

        $params   = [
            'organiser_id' => Auth::id(),
        ];

        $myevents    = $this->event->get_all_myevents($params);

        if(empty($myevents))
            return error(__('eventmie-pro::em.event').' '.__('eventmie-pro::em.not_found'), Response::HTTP_BAD_REQUEST );
        
        return response([
            'myevents'=> $myevents,
        ], Response::HTTP_OK);

    }


}