<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
     /**
     * getThumbAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getThumbAttribute($value)
    {
        if(!empty($value)) {
            // Use Storage facade to generate correct URL based on disk configuration
            return \Storage::url($value);
        }
        
        return $value;
    }
    // get categories
    public function get_categories()
    {   
        $category = Category::where(['status' => 1])->get();

        return to_array($category);
    }

    // get event category
    public function get_event_category($id = null)
    {   
        $category = Category::where(['id' => $id, 'status' => 1])->first();

        return to_array($category);
    }
}
