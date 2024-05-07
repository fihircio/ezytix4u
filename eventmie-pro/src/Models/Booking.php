<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Models\Transaction;
use Classiebit\Eventmie\Models\Commission;

use Classiebit\Eventmie\Scopes\BulkScope;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    protected $guarded = [];

    /**
     * Table used
    */
    private $tb                 = 'bookings';
    private $tb_tickets         = 'tickets'; 
    protected $with             = ['commission'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        
        if(!empty(\Request::route())) {
            if(\Request::route()->getName() != 'voyager.bookings.bulk_bookings' && \Request::route()->getName() != 'eventmie.get_booking' && \Request::route()->getName() != 'eventmie.verify_ticket')
            {
                static::addGlobalScope(new BulkScope);
            }
            
            if(\Request::route()->getName() == 'voyager.bookings.bulk_bookings')
            {
                static::addGlobalScope('bulk_scope', function (Builder $builder) {
                    $builder->where(['is_bulk' => 1]);
                });
            }
        }
        
    }
    
    // make booking
    public function make_booking($params = [])
    {
        return Booking::create($params);
    }    

    // get booking for customer
    public function get_my_bookings($params = [])
    {   
        return Booking::select('bookings.*', 'E.slug as event_slug', 'E.excerpt as event_excerpt', 'E.venue as event_venue', 'E.thumbnail as event_thumbnail')
                ->from('bookings')
                ->leftJoin('events as E', 'E.id', '=', 'bookings.event_id')
                ->selectRaw("(SELECT E.online_location FROM events E WHERE E.id = bookings.event_id AND bookings.is_paid = 1  AND bookings.status = 1) online_location")
                ->where(['bookings.customer_id' => $params['customer_id'] ])
                ->orderBy('bookings.id', 'desc')
                ->paginate(20);
    }
    

    // check booking id for cancellation
    public function check_booking($params = [])
    {
        return Booking::
            where([
                'status'        => 1, 
                'customer_id'   => $params['customer_id'], 
                'id'            => $params['booking_id'], 
                'ticket_id'     => $params['ticket_id'], 
                'event_id'      => $params['event_id'] ])
            ->first();   
    }

    // booking_cancel for customer
    public function booking_cancel($params = [])
    {
        return Booking::
                where([
                    'status'        => 1, 
                    'checked_in'    => 0, 
                    'customer_id'   => $params['customer_id'], 
                    'id'            => $params['booking_id'], 
                    'ticket_id'     => $params['ticket_id'], 
                    'event_id'      => $params['event_id'] ])
                ->update(['booking_cancel' => 1 ]);
    }

    /**
     * ================Organiser Booking Start=========================================
     */

     // get booking for organiser
    public function get_organiser_bookings($params = [])
    {
        $query = Booking::query();
        
        $query->select('bookings.*', 'CM.customer_paid', 'E.slug as event_slug', 'E.thumbnail as event_thumbnail')
            ->from('bookings')
            ->leftJoin('events as E', 'E.id', '=', 'bookings.event_id')
            ->selectRaw("(SELECT E.online_location FROM events E WHERE E.id = bookings.event_id AND bookings.is_paid = 1  AND bookings.status = 1) online_location")
            ->leftJoin('commissions as CM', 'CM.booking_id', '=', 'bookings.id');
            
            // in case of searching by between two dates
            if(!empty($params['start_date']) && !empty($params['end_date']))
            {
                $query ->whereDate('bookings.created_at', '>=' , $params['start_date']);
                $query ->whereDate('bookings.created_at', '<=' , $params['end_date']);
            }
            
            // in case of searching by start_date
            if(!empty($params['start_date']) && empty($params['end_date']))
                $query ->whereDate('bookings.created_at', $params['start_date']);

            // in case of searching by event_id
            if($params['event_id'] > 0)
                $query->where(['bookings.event_id' => $params['event_id']]);

            
        return  $query->where([ 'bookings.organiser_id' => $params['organiser_id'] ])
                ->orderBy('id', 'desc')
                ->paginate(20);
    }
    
    // check booking id for cancellation for organiser
    public function organiser_check_booking($params = [])
    {
        return Booking::where($params)->first();   
    }

    // booking_edit for customer by organiser
    public function organiser_edit_booking($data = [], $params = [])
    {
        return Booking::where($params)->update($data);
    }

    // organiser view booking of customer
    public function organiser_view_booking($params = [])
    {
        return Booking::select('bookings.*')->from('bookings')
            ->where($params)
            ->first();  
    }

    // only admin can delete booking
    public function delete_booking($params = [])
    {
        // delete commission after deleting booking
        DB::transaction(function () use ($params) {
            DB::table($this->tb)->where($params)->delete();
            DB::table("commissions")->where(['booking_id'=>$params['id']])->delete();
        });

        return true;
    }

    // only admin and organiser can get particular event's booking
    public function get_event_bookings($params = [], $select = ['*'])
    {
        $booking = Booking::select($select)->where($params)->get();

        return to_array($booking);
    }

    /**
     *  total booking coutn
     */

    public function total_bookings($user_id = null)
    {
        if(!empty($user_id))
        {
            return Booking::where(['organiser_id' => $user_id])->count();
        }
        return Booking::count();
    } 

    /**
     *  total revenue count
     */

    public function total_revenue($user_id = null)
    {
        if(!empty($user_id))
        {
            return Booking::where(['organiser_id' => $user_id, 'status' => 1])->sum('net_price');
        }
        return Booking::where(['status' => 1])->sum('net_price');

    } 


    // update payment_type only when upgrading to v1.3.x
    public static function update_payment_type()
    {
        $bookings = Booking::get();
        if($bookings->isNotEmpty())
        {
            foreach($bookings as $key => $value)
            {   
                // offline
                if($value->transaction_id == 0 && $value->net_price > 0)
                {
                    Booking::where(['id' => $value->id])->update(['payment_type' => 'offline']);
                }
            }
        }    
    }
    
    // sum booked ticket quantity each booking date + each ticket idd
    public function get_seat_availability_by_ticket($event_id = null)
    {
        return DB::table($this->tb)
                ->select('event_start_date', 'ticket_id')
                ->selectRaw("SUM(quantity) as total_booked")
                ->where("event_id", $event_id)
                ->where("status", 1)
                ->groupBy("event_start_date", "ticket_id")
                ->orderBy('ticket_id')
                ->get();
    }
    
    /**
     * commission
     *
     * @return void
     */
    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    /**
     * agent
     *
     * @return void
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * pos
     *
     * @return void
     */
    public function pos()
    {
        return $this->belongsTo(User::class, 'pos_id');
    }

    /**
     * sponsor
     *
     * @return void
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }
        
    /**
     * getTransaction
     *
     * @return void
     */
    public function getTransaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
