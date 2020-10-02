<?php

namespace App\Traits;

use Illuminate\Http\Request;


trait Slug
{
    protected function slugText(Request $request, $name)
    {
        $string = str_replace(' ', '-', $request->$name); // Replaces all spaces with hyphens.
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)) . '-' . base64_encode(rand(100, 999)); // Removes special chars.
    }

    protected function sku($request, $id)
    {
        $string = str_replace(' ', '-', json_encode($request['variant'])); // Replaces all spaces with hyphens.
        $string = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)) . base64_encode($id . rand(100, 999)); // Removes special chars.
        return strtoupper($string);
    }

    protected function username($request)
    {
        $string = str_replace(' ', '', $request); // Replaces all spaces with hyphens.
        return strtolower(preg_replace('/[^a-zA-Z0-9_ -]/s', '', $string) . base64_encode(rand(100, 999))); // Removes special chars.
    }
}
