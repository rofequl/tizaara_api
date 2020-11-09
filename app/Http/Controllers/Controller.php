<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function saveAddress($address, $request)
    {
        $address->country_id = $request->country;
        $address->division_id = $request->division;
        $address->city_id = $request->city;
        $address->area_id = $request->area;
        $address->address = $request->address;
        $address->zip_code = $request->zip_code;
        $address->save();
        return $address->id;
    }

}
