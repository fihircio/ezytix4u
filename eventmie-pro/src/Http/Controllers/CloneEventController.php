<?php

namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Classiebit\Eventmie\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Classiebit\Eventmie\Models\Event;
use Classiebit\Eventmie\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class CloneEventController extends Controller
{
    public $organiser_id;

    public function __construct()
    {
        // language change
        $this->middleware('common');

        $this->middleware(['organiser']);

        $this->schedule = new Schedule();
        $this->event    = new Event();
        
    }
    /**
     *  clone event
     */

    public function clone_event(Event $event)
    {   
        //admin and organiser both can clone event
        $this->is_admin($event);

        // check owner of this event
        $check_event            = $this->event->get_user_event($event->id, $this->organiser_id);

        if(empty($check_event))
        {
            return error('access denied!', Response::HTTP_BAD_REQUEST );
        }
       
        // clone event
        $new_event = $event->replicate();
        
        //save new event
        $this->save_event($check_event ,$new_event);

        //redirect
        $url = route('eventmie.myevents_index');

        // redirect no matter what so that it never turns back
        $msg = __('eventmie-pro::em.clone_success');
        
        session()->flash('status', $msg);

        return success_redirect($msg, $url);
    }


    /**
     * save clone event
     */

    private function save_event($old_event = null, $new_event = null)
    {
        // create random string
        $random_number       = \Str::random(5);
        
        // update title and slug for new event
        $new_event->title   = $new_event->title.' '.$random_number;
        $new_event->slug    = $new_event->slug.'-'.$random_number;  
        $new_event->short_url    = $new_event->short_url.'-'.$random_number;  
        $new_event->publish = 0;  
        $new_event->status  = 1;  

        // convert into array
        $new_event = $new_event->makeVisible(['online_location', 'youtube_embed', 'vimeo_embed'])->toArray();
        
        // save new event
        $new_event = Event::create($new_event);


        // save schedule
        $this->save_schedules($old_event, $new_event);

        //save tags
        $this->save_tags($old_event, $new_event);

        //save tickets
        $this->save_tickets($old_event, $new_event);

        // save seatchart
       // $this->save_seatchart($old_event, $new_event);

        // save seats
        //$this->save_seats($old_event, $new_event);


    }

    /**
     *   save schedules if old event have schedules
     */

    private function save_schedules($old_event = null, $new_event = null)
    {   
     
        $params      = [
            'event_id'  => $old_event->id,
            'user_id'   => $this->organiser_id, // organizer_id
        ];
        
        // get old schedules
        $schedules   = $this->schedule->get_schedule($params);
        
        if(empty($schedules))
            return true;
        
        //convert into collection;
        $schedules  = collect($schedules);

        // update old schedule for new event
        $schedules  = $schedules->map(function ($item, $key) use($new_event) {
            //update event_id by new event_id
            
            unset($item['id']); // unset schedule id
            $item['event_id']   = $new_event->id; // new event id
            $item['created_at'] = \Carbon\Carbon::now()->toDateTimeString();
            $item['updated_at'] = \Carbon\Carbon::now()->toDateTimeString(); 
            return $item;
        });

        //convert into array
        $schedules  = $schedules->all();
        
        // save new schedule for new event
        Schedule::insert($schedules);
    
     
    }

    /**
     *   save tags if old event have tags
     */

    private function save_tags($old_event = null, $new_event = null)
    {
        $tags = $this->event->selected_event_tags($old_event->id);
        
        // if tags empty then return
        if($tags->isEmpty())
            return true;
            
        // update tags for new event
        $tags_ids  = $tags->map(function ($item, $key)  {
            
            return $item->id;
        });

        $params = [
            'event_tags'     => $tags_ids->toArray(),
            "event_id"       => $new_event->id,
        ];
        
         $this->event->event_tags($params);
        
    }

    /**
     *  save tickets if old event have tickets
     */

    private function save_tickets($old_event = null, $new_event = null)
    {
        
        if($old_event->tickets->isEmpty())
            return true;
        
        $old_event->tickets->map(function ($value, $key) use ($new_event){
            
            $newValue = $value->replicate();
            
            $newValue->event_id    = $new_event->id; // new event id
            $newValue->created_at  = \Carbon\Carbon::now()->toDateTimeString();
            $newValue->updated_at  = \Carbon\Carbon::now()->toDateTimeString(); 

            $newValue->save();
        });

        
    }

    /**
     *  save seatchart
     */
    
    private  function save_seatchart($old_event = null, $new_event = null)
    {
        if($old_event->tickets->isEmpty())
            return true;

        $old_event = \Classiebit\Eventmie\Models\Event::where(['id' => $old_event->id])->first();
        
        $old_event->tickets->map(function ($value, $key) use ($new_event){
        
            $seatchart = \Classiebit\Eventmie\Models\Ticket::where(['id' => $value->id])->first()->seatchart;
           
            if(!empty($seatchart))
            {
                $newValue = $seatchart->replicate();
                 
                $newValue->event_id    = $new_event->id; // new event id
                $newValue->ticket_id   = $new_event->tickets[$key]->id; // new ticket id
                $newValue->created_at  = \Carbon\Carbon::now()->toDateTimeString();
                $newValue->updated_at  = \Carbon\Carbon::now()->toDateTimeString(); 

                $newValue->save();
            }    
        });
        
    }
    
    /**
     *  save seats
     */
    
    private  function save_seats($old_event = null, $new_event = null)
    {
        if($old_event->tickets->isEmpty())
            return true;

        $old_event = \Classiebit\Eventmie\Models\Event::with(['tickets', 'tickets.seatchart', 'tickets.seatchart.seats'])->where(['id' => $old_event->id])->first();
        

        $new_event = \Classiebit\Eventmie\Models\Event::with(['tickets', 'tickets.seatchart', 'tickets.seatchart.seats'])->where(['id' => $new_event->id])->first();
        
        $old_event->tickets->map(function ($ticket, $key) use ($new_event){
        
            if(!empty($ticket->seatchart) && $ticket->seatchart->seats->isNotEmpty())
            {
                $ticket->seatchart->seats->map(function ($seat, $k) use ($new_event, $key){
                    
                    $newValue = $seat->replicate();
                 
                    $newValue->event_id      = $new_event->id; // new event id
                    $newValue->ticket_id     = $new_event->tickets[$key]->id; // new ticket id
                    $newValue->seatchart_id  = $new_event->tickets[$key]->seatchart->id; // new seatchart id
                    $newValue->created_at    = \Carbon\Carbon::now()->toDateTimeString();
                    $newValue->updated_at    = \Carbon\Carbon::now()->toDateTimeString(); 

                    $newValue->save();
                });

            }    
        });
        
    }
    
    // admin can also clone event
    protected function is_admin($old_event = null)
    {
        $this->organiser_id = Auth::id();
        
        if(Auth::user()->hasRole('admin'))
        {
            $this->organiser_id = $old_event->user_id;
        }

    }
}
