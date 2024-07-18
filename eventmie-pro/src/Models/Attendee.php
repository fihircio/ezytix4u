<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Classiebit\Eventmie\Models\Booking;
use Classiebit\Eventmie\Models\Seat;

class Attendee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    /**
     * Get the booking record associated with the attendee.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the seat record associated with the attendee.
     */
    public function seat()
    {
        return $this->belongsTo(seat::class);
    }
}
