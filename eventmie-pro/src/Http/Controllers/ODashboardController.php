<?php 
namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Facades\Classiebit\Eventmie\Eventmie;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Classiebit\Eventmie\Models\Booking;
use Classiebit\Eventmie\Charts\EventChart;
use Classiebit\Eventmie\Models\Event;
class ODashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(['organiser']);
        $this->event = new Event;
    }
    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {  
        $organizer = Auth::user();
        $total_events = $organizer->events->count();
        $total_earning = $organizer->events->pluck('bookings')->flatten()->where('status', 1)->where('is_paid', 1)->pluck('commission')->filter()->sum('organiser_earning');
        $total_bookings = $organizer->events->pluck('bookings')->flatten()->where('status', 1)->where('is_paid', 1)->count();

        $top_selling_events       = $this->event->top_selling_events(Auth::id());
        $labels = [];
        $values = [];
        if(!empty($top_selling_events))
        {
            foreach($top_selling_events as $val)
            {
                $labels[] = strlen($val['title']) > 25 ? mb_substr($val['title'], 0, 25, 'utf-8')."..." : $val['title'];
                $values[] = $val['total_bookings'];
            }
        }
        $eventsChart = new EventChart;
        $eventsChart
        ->labels($labels)
        ->dataset(__('voyager::generic.total').' '.__('voyager::generic.Bookings'), 'bar', $values)
        ->color("rgba(27, 137, 239, 1)")
        ->backgroundcolor("rgba(26, 136, 239, 0.7)");
        
        return view('eventmie::o_dashboard.dashboard', compact('total_earning', 'total_events', 'total_bookings', 'eventsChart'));
    }
    
    /**
     * organizer_booking_revenue
     *
     * @return void
     */
    public function organizer_booking_revenue()
    {
        $user = Auth::user();

        $monthly_bookings_revenue = Booking::select('created_at',\DB::raw('COUNT(id) as monthly_bookings'))
            ->addSelect(['total_revenue' => Booking::from('bookings as B')->select(\DB::raw('sum(B.net_price)'))
            ->where(\DB::raw('DATE_FORMAT(B.created_at, "%Y %m")'), '=',  \DB::raw('DATE_FORMAT(bookings.created_at, "%Y %m")'))
            ->where('net_price', '>', 0)->groupBy(\DB::raw('DATE_FORMAT(B.created_at, "%Y %m")'))
            ->where(['B.organiser_id' => $user->id])
            ->limit(1)
        ])
        ->where(['organiser_id' => $user->id])
        ->groupBy(\DB::raw('DATE_FORMAT(created_at, "%Y %m")'))
        ->get()
        ->toArray();
        
        

        $data = [];

        foreach($monthly_bookings_revenue as $key => $value)
        {
            $data[$key]['month'] = \Carbon\Carbon::parse($value['created_at'])->format('Y-m');
            $data[$key]['full_month'] = \Carbon\Carbon::parse($value['created_at'])->format('F').' ( '.\Carbon\Carbon::parse($value['created_at'])->format('Y').' )';
            $data[$key]['total_bookings'] = $value['monthly_bookings'];
            $data[$key]['total_revenue'] = !empty($value['total_revenue']) ? $value['total_revenue'] : 0;
        }
        
        return response()->json($data); 

    }

    public function luckyDraw(Request $request)
    {
        // Validate the request (ensure event_id is provided)
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
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

}