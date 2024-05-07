<?php

namespace Classiebit\Eventmie\Http\Controllers;


use App\Http\Controllers\Controller; 
use Facades\Classiebit\Eventmie\Eventmie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Classiebit\Eventmie\Models\Booking;
use Illuminate\Support\Carbon;
use Auth;
use Classiebit\Eventmie\Models\Event;

class TicketScanController extends Controller
{
    public function __construct()
    {
        // language change
        $this->middleware('organiser');
        $this->booking      = new Booking;
    }

    // ticket scan
    public function index(Request $request, $view = 'eventmie::ticket_scan.index', $extra = [])
    {
        return Eventmie::view($view, compact('extra'));
    }

    public function get_booking(Request $request)
    {
        $request->validate([
            'id'            => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
            'order_number'  => 'required',
        ]);

        $booking = Booking::where(['id'=>$request->id, 'order_number'=>$request->order_number])->first();

        if(empty($booking))
        {
            
            return response()->json([
                    'errors' => [
                        'msg' => [__('eventmie-pro::em.ticket').' '.__('eventmie-pro::em.not_found')]
                    ]
            ], 422);
        
        }

        // if($booking->event_start_date != Carbon::today()->format('Y-m-d'))
        // {
        //     return response()->json([
        //             'errors' => [
        //                 'msg' => [__('eventmie-pro::em.ticket_scan_error')]
        //             ]
        //     ], 422);
        // }
        

        $event = Event::find($booking->event_id);


        if($booking->checked_in == $booking->quantity && empty($event->merge_schedule)) 
        {
            $msg = __('eventmie-pro::em.already_cheked_in');
                    
            $request->validate([
                'already_scan' => 'required'
            ],[
                'already_scan.required' => $msg
            ]);
        }

        if(!empty($event->merge_schedule))
        {
            $scan_date   = null;
            $merge_dates = \DB::table('serverside_dates')->whereDate('from_date', Carbon::parse($booking->event_start_date)->format('Y-m').'-01')->where(['event_id' => $event->id])->first();
            
            if(!empty($merge_dates))
            {
                $merge_dates = json_decode($merge_dates->dates, true);

                $merge_dates = collect($merge_dates)->map(function ($item, $key) {
                    return  Carbon::parse($item)->format('Y-m-d');
                });

                $date_exist = $merge_dates->search(function ($item, $key) {
                    return $item == Carbon::today()->format('Y-m-d');
                });

                if(!is_numeric($date_exist)) {
                    $msg = __('eventmie-pro::em.booking').' '.__('eventmie-pro::em.not_found').' OR '.__('eventmie-pro::em.ticket_scan_error');
                    $request->validate([
                        'already_scan' => 'required'
                    ],[
                        'already_scan.required' => $msg
                    ]);
                }

                $scan_date = $merge_dates[$date_exist];
                
                $already_scan = \DB::table('merge_scan_bookings')->where(['event_id' => $event->id, 'booking_id' => $booking->id])->whereDate('event_start_date', $scan_date)->first();

                if(!empty($already_scan)) {
                    $msg = __('eventmie-pro::em.already_cheked_in');
                    
                    $request->validate([
                        'already_scan' => 'required'
                    ],[
                        'already_scan.required' => $msg
                    ]);


                }
            }
        }
        
        //  CUSTOM

        return response()->json(['status' => true, 'booking' => $booking ]);
    }

    // verify tikcet after scan
    public function verify_ticket(Request $request, $organiser_id = null)
    {
        $request->validate([
            'booking_id'          => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
            'order_number'        => 'required',
            
        ]);
        
        $params = [
            'id'            => $request->booking_id,
            'order_number'  => $request->order_number,
        ];

        // so that we can pass organizer other than logged in user
        if(!$organiser_id)
            $organiser_id = Auth::id();
        
        // check for organizer id except for Admin
        if(!Auth::user()->hasRole('admin'))
            $params['organiser_id'] = $organiser_id;
        
        // check booking 
        // if it's organizer's booking
        // and ticket already scan or not
        $booking = $this->booking->organiser_check_booking($params);

        $event = Event::find($booking->event_id);


        // ticket already scan then show error message
        if(empty($booking))
        {
            $msg = __('eventmie-pro::em.ticket').' '.__('eventmie-pro::em.not_found');
            session()->flash('error', $msg);
            return error_redirect($msg);
        }

        if($booking->status != 1) 
        {
            $msg = __('eventmie-pro::em.disabled_ticket');
            session()->flash('error', $msg);
            return error_redirect($msg);
        }

        if($booking->is_paid != 1) 
        {
            $msg = __('eventmie-pro::em.disabled_ticket');
            session()->flash('error', $msg);
            return error_redirect($msg);
        }

        if($booking->checked_in == $booking->quantity && empty($event->merge_schedule)) 
        {
            $msg = __('eventmie-pro::em.already_cheked_in');
            session()->flash('error', $msg);
            return error_redirect($msg);
        }


        if(!empty($event->merge_schedule))
        {
            $scan_date   = null;
            $merge_dates = \DB::table('serverside_dates')->whereDate('from_date', Carbon::parse($booking->event_start_date)->format('Y-m').'-01')->where(['event_id' => $event->id])->first();
            
            if(!empty($merge_dates))
            {
                $merge_dates = json_decode($merge_dates->dates, true);

                $merge_dates = collect($merge_dates)->map(function ($item, $key) {
                    return  Carbon::parse($item)->format('Y-m-d');
                });

                $date_exist = $merge_dates->search(function ($item, $key) {
                    return $item == Carbon::today()->format('Y-m-d');
                });


                if(!is_numeric($date_exist)) {
                    $msg = __('eventmie-pro::em.booking').' '.__('eventmie-pro::em.not_found');
                    session()->flash('error', $msg);
                    return error_redirect($msg);
                }


                $scan_date = $merge_dates[$date_exist];
                
                $already_scan = \DB::table('merge_scan_bookings')->where(['event_id' => $event->id, 'booking_id' => $booking->id])->whereDate('event_start_date', $scan_date)->first();

                if(!empty($already_scan)) {
                    $msg = __('eventmie-pro::em.already_cheked_in');
                    session()->flash('error', $msg);
                    return error_redirect($msg);
                }

                \DB::table('merge_scan_bookings')->insert([
                    'event_id'   => $event->id,
                    'booking_id' => $booking->id,
                    'event_start_date' => $scan_date,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);
                    
            }
        }

        $data = [
            'checked_in' => $booking->checked_in + 1,
            'checked_in_time' => Carbon::now()->toDateTimeString()
        ];
       
        // update checked_in by 1        
        $booking = $this->booking->organiser_edit_booking($data, $params);
        
        $url = route('eventmie.ticket_scan');
        $msg = __('eventmie-pro::em.success_cheked_in');
        
        session()->flash('status', $msg);
        return success_redirect($msg, $url);
    
    }
   
}
