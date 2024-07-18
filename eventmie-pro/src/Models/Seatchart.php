<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Classiebit\Eventmie\Models\Seat;

class Seatchart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Get the seats for the seatchart.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
