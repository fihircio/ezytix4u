<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Classiebit\Eventmie\Models\Tag;
use Classiebit\Eventmie\Models\Booking;
use Classiebit\Eventmie\Models\commission;
use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Models\Country;

class Venue extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get events with 
     * pagination and custom selection
     * 
     * @return string
     */
    public function venues($params  = [])
    {   
        $query = Venue::query()->with(['country']); 
    
        if(!empty($params['search']))    
        {
            $query
            ->whereRaw("( title LIKE '%".$params['search']."%' 
                 OR state LIKE '%".$params['search']."%' OR city LIKE '%".$params['search']."%')");
        }

        if(!empty($params['city']))
        {
            $query
            ->where('city','LIKE',"%{$params['city']}%");
        }

        if(!empty($params['state']))
        {
            $query
            ->where('state','LIKE',"%{$params['state']}%");
        }
        
        return $query
        ->where(["status" => 1])->orderBy('title', 'ASC')->paginate(9);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the country associated with the venue.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * getImagesAttribute
     * Convert venue image paths to proper URLs
     *
     * @param  mixed $value
     * @return string
     */
    protected $appends = ['images_url'];

    /**
     * getImagesUrlAttribute
     * Convert venue image paths to proper URLs
     *
     * @param  mixed $value
     * @return string
     */
    public function getImagesUrlAttribute()
    {
        $value = $this->images;
        if(!empty($value)) {
            // Decode the JSON array of image paths
            $images = json_decode($value, true);
            
            if(is_array($images)) {
                // Convert each path to proper URL
                $images = array_map(function($path) {
                    if(!empty($path)) {
                        // If it's already a full URL (http/https), return as-is
                        if(filter_var($path, FILTER_VALIDATE_URL)) {
                            return $path;
                        }
                        // Use Storage facade to generate correct URL based on disk configuration
                        return \Storage::url($path);
                    }
                    return $path;
                }, $images);
                
                // Return as JSON string to maintain compatibility
                return json_encode($images);
            }
        }

        return $value;
    }
    
}
