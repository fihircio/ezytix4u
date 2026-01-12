<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends \TCG\Voyager\Models\Post
{
    protected $guarded = [];
    protected $translatable = [];
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
    // posts with limit for welcome page
    public function index()
    {
        $result = Post::where(['status' => 'PUBLISHED'])
                    ->limit(3)->orderBy('updated_at', 'DESC')->get();

        return $result;

    }

    // particular post view
    public function view($slug = null)
    {
        return Post::where(['slug' => $slug])->first();
        
    }

    // get posts
    public function get_posts()
    {
        return Post::where(['status' => 'PUBLISHED'])->orderBy('updated_at', 'DESC')->paginate(9);
    }

}    