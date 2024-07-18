<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Classiebit\Eventmie\Models\Attendee;

class Seat extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Get the attendees record associated with the seat.
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }
}
