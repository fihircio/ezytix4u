<?php

namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Facades\Classiebit\Eventmie\Eventmie;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Classiebit\Eventmie\Models\Page;
use Classiebit\Eventmie\Models\Event;


class PagesController extends Controller
{

    public function __construct()
    {
        // language change
        $this->middleware('common');
    }
    
    // get featured events
    public function view($page = null, $view = 'eventmie::pages', $extra = [])
    {
        // First find event via Short_url
        $event  = Event::where(['short_url' => $page])->first();
        if($event) {
            // redirect to event page
            return redirect(route('eventmie.events_show', [$event->slug]));
        }

        $page   = Page::where(['slug' => $page])->firstOrFail();
        return Eventmie::view($view, compact('page', 'extra'));
   }
}    