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
     * getChartImageAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getChartImageAttribute($value)
    {
        if(!empty($value)) {
            // If it's already a full URL (http/https), return as-is
            if(filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            // Use Storage facade to generate correct URL based on disk configuration
            return \Storage::url($value);
        }

        return $value;
    }

    /**
     * Get the seats for the seatchart.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
