<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\File;

trait FileUpload
{
    protected function saveImages(Request $request, $file, $folder, $width = null, $height = null)
    {
        $path = public_path() . '/' . $folder;
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $name = base64_encode(rand(1000, 9999) . time()) . '.' . explode('/', explode(':', substr($request->$file, 0, strpos($request->$file, ';')))[1])[1];
        Image::make($request->$file)->resize($width, $height)->save($path . '/' . $name);
        return $folder . $name;
    }

    protected function saveImagesVue($request, $file, $folder)
    {
        $path = public_path() . '/' . $folder;
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        $name = base64_encode(rand(1000, 9999) . time()) . '.' . explode('/', explode(':', substr($request[$file], 0, strpos($request[$file], ';')))[1])[1];
        Image::make($request[$file])->save($path . '/' . $name);
        return $folder . $name;
    }

    protected function saveImagesVue2($request, $folder)
    {
        $path = public_path() . '/' . $folder;
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $name = base64_encode(rand(1000, 9999) . time()) . '.' . explode('/', explode(':', substr($request, 0, strpos($request, ';')))[1])[1];
        Image::make($request)->save($path . '/' . $name);
        return $folder . $name;
    }
}
