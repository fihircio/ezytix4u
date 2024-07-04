<?php

namespace Classiebit\Eventmie\Models;


use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;
use DB;
use Illuminate\Support\Carbon;

class Promocode extends Model
{
    protected $guarded = [];
    
    // get promocodes
    public function get_promocodes()
    {
        return Promocode::where(['status' => 1])->get()->toArray();
    }

    // save promocodes with ticket' id in relational table
    public function save_ticket_promocode($params = [], $ticket_id = null)
    {   
        DB::table('ticket_promocode')
            ->where(['ticket_id' => $ticket_id])
            ->delete();

        return DB::table('ticket_promocode')->insert($params);
    }

    // get particular promocodes's ids for particular ticket
    public function get_ticket_promocodes_ids($params = [])
    {
        return  DB::table('ticket_promocode')
                            ->select('promocode_id')
                            ->where(['ticket_id' => $params['ticket_id']])
                            ->get()
                            ->toArray();
        
    }

    // get particular ticket's promocode 
    public function get_ticket_promocodes($params = [])
    {
        return Promocode::whereIn('id',$params['promocodes_ids'])
            ->where(['status' => 1])
            ->get()
            ->toArray();
    }

    // check user used promocode or not
    public function promocode_user($params = [])
    {
        return DB::table('promocode_user')->where($params)
                                          ->first();
    }

    // check user used promocode or not
    public function promocode_apply($params = [])
    {
        // check if already applied
        // promocode_user -> user_id && promocode_id && ticket_id

        // insert into promocode_user 
        // promocode_user -> user_id && promocode_id && ticket_id
        $promocode_apply  = DB::table('promocode_user')->where($params)
                                          ->first();
        
        if(empty($promocode_apply))
        {
            $params['used_at'] = Carbon::now();
            $promocode_apply =  DB::table('promocode_user')->insert($params);

            // promocode quantity decrement
            if(!empty($promocode_apply))
            {
                $this->promocode_quantity($params['promocode_id']);
            }
        }
        
        return $promocode_apply;
    }

    // promocode quantity decrement 
    public function promocode_quantity($promocode_id = null)
    {
        $promocode = Promocode::where(['id' => $promocode_id])->first();

        if (!is_null($promocode->quantity) && $promocode->quantity > 0 ) {
            $promocode->quantity -= 1;
            $promocode->save();
        }
    }


}    