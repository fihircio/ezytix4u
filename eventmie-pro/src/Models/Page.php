<?php

namespace Classiebit\Eventmie\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends \TCG\Voyager\Models\Page
{
    protected $translatable = [];

    /**
     * getBodyAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getBodyAttribute($value)
    {
        if(!empty($value)) {
            // Replace /storage/path with S3 URL, avoiding empty paths
            return preg_replace_callback('/\/storage\/([^"\'>\s]*)/', function($matches) {
                $path = $matches[1];
                if (!empty($path)) {
                    return \Storage::url($path);
                }
                return $matches[0]; // Leave unchanged if empty
            }, $value);
        }
        
        return $value;
    }
}    