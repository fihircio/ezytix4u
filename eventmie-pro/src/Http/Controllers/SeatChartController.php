<?php
namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Classiebit\Eventmie\Models\Seatchart;
use Classiebit\Eventmie\Models\Ticket;

class SeatChartController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // language change
        $this->middleware('common');
    
        // authenticate except login_first
        $this->middleware('auth');

    }
    
    public function upload_seatchart(Request  $request)
    {
        // 1. validate data
        $request->validate([
            'file'        => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'event_id'    => 'required|numeric|gt:0',
            'ticket_id'   =>  'required|numeric|gt:0',
        ]);

        $path            = 'seatschart/'.Carbon::now()->format('FY').'/';

        $file            = $request->file('file');
        $extension       = $file->getClientOriginalExtension(); // getting image extension
        $image           = time().rand(1,988).'.'.$extension;
        
        if (app('env') === 'production') 
        {

            $file->storeAs(''.$path, $image);
            
        } else 
        {
            $file->storeAs('public/'.$path, $image);
        }

        $chart_image           = $path.$image;

        $params = [
            'ticket_id'   => $request->ticket_id,
            'event_id'    => $request->event_id,
            'chart_image' => $chart_image
        ];

        // if ticket_id and event_id exist then will update image unless create 
        Seatchart::updateOrCreate(
            [ 'ticket_id' => $params['ticket_id'], 'event_id' => $params['event_id'] ],
            
            $params
        
        );

        $ticket     = Ticket::with(['seatchart', 
                        'seatchart.seats'  => function ($query) {
                            // $query->where(['status' => 1]);
                        }
                    ])->where(['id' => $request->ticket_id])->first();

        return response()->json(['ticket' => $ticket, 'status' => true]);
        
    }

    /**
     *  disable or enable seatchart
     */

    public function disable_enable_seatchart(Request $request)
    {
        // 1. validate data
        $request->validate([
            'ticket_id'   =>  'required|numeric|gt:0',
        ]);

        $ticket = Ticket::with(['seatchart'])->where(['id' => $request->ticket_id])->first();

        if(empty($ticket))
            return response()->json(['status' => false, 'error' => __('eventmie-pro::em.ticket_not_found')]);
            
        if(empty($ticket->seatchart))
            return response()->json(['status' => false, 'error' => __('eventmie-pro::em.seatchart_not_found')]);
    
        $params = [
            'status'   => !$ticket->seatchart->status,
    
        ];

     
        // if ticket_id and event_id exist then will update image unless create 
        Seatchart::updateOrCreate(
            [ 'ticket_id' => $ticket->id, 'event_id' => $ticket->event_id ],
            
            $params
        
        );


        return response()->json(['status' => true]);
    }
}
