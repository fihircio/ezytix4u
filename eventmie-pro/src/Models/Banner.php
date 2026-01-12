<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = [];

    /**
     * getImageAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getImageAttribute($value)
    {
        if(!empty($value)) {
            // If it's already a full URL, return it as is
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            // Otherwise, use Storage facade to generate correct URL based on disk configuration
            return \Storage::url($value);
        }
        
        return $value;
    }

    public function get_banners()
    {
        return Banner::where(['status' => 1])->orderBy('order', 'asc')->get();
    }

}    