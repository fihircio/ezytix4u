<?php

namespace Classiebit\Eventmie\Http\Controllers\Voyager;
use Facades\Classiebit\Eventmie\Eventmie;

use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends BaseVoyagerController
{
    public function index()
    {
        return Eventmie::view('eventmie::vendor.voyager.dashboard');
    }

    public function logout()
    {
        Auth::logout();

        return redirect(config('eventmie.route.prefix').'/'.config('eventmie.route.admin_prefix'));
    }

    public function upload(Request $request)
    {
        $valid_ext = ['jpg','jpeg','png','gif','bmp','svg'];
        $file = $request->file('image');
        $path = $request->get('upload_path', '/');
        $name = Voyager::image($file, $path);
        $fullPath = $path . $name;
        $image = Image::make($file)->encode($file->getClientOriginalExtension(), 75);
        Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image);
        return response()->json(['path' => $fullPath]);
    }
}
