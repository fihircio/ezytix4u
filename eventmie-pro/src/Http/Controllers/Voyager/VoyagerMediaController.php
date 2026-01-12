<?php

namespace Classiebit\Eventmie\Http\Controllers\Voyager;

use TCG\Voyager\Http\Controllers\VoyagerMediaController as BaseVoyagerMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use TCG\Voyager\Facades\Voyager;

class VoyagerMediaController extends BaseVoyagerMediaController
{
    public function upload(Request $request)
    {
        // This is a simplified override - in practice, you'd copy the full base method
        // and remove 'public' from Storage::put calls

        $path = $request->upload_path;
        $file = $request->file;
        $name = $request->name ?? $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Store the file without visibility parameter
        $filePath = $file->storeAs($path, $name.'.'.$extension, $this->filesystem);

        // Handle thumbnails if needed
        if ($this->isImage($extension)) {
            $thumbnail = Image::make($file)->fit(250, 160)->encode($extension, 75);
            $thumbnail_file = $path . '/thumbs/' . $name . '.' . $extension;
            Storage::disk($this->filesystem)->put($thumbnail_file, (string) $thumbnail);
        }

        return response()->json(['success' => true, 'file' => $filePath]);
    }

    private function isImage($extension)
    {
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
    }
}
