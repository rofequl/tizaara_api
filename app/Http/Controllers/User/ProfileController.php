<?php

namespace App\Http\Controllers\User;

use App\Address;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    public function index()
    {
        $user = User::where('id', Auth::user()->id)->select('id', 'first_name', 'last_name', 'job_title', 'email', 'mobile', 'telephone', 'photo', 'photo_type')->first();
        $address = Address::where('addressable_id', $user->id)->where('address_type', 'user')->first();
        if (!$address) {
            $user['status'] = "new";
            return $user;
        }
        $user['status'] = "old";
        $user['country'] = $address->country_id;
        $user['division'] = $address->division_id;
        $user['city'] = $address->city_id;
        $user['area'] = $address->area_id;
        $user['address'] = $address->address;
        $user['zip_code'] = $address->zip_code;
        return $user;
    }

    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->job_title = $request->job_title;
        $user->telephone = $request->telephone_no;
        $user->save();
        $address = Address::where('addressable_id', $user->id)->where('address_type', 'user')->first();
        if ($address) {
            $address->country_id = $request->country;
            $address->division_id = $request->division;
            $address->city_id = $request->city;
            $address->area_id = $request->area;
            $address->address = $request->address;
            $address->zip_code = $request->zip_code;
            $address->save();
        } else {
            $address = new Address();
            $address->addressable_id = $user->id;
            $address->address_type = 'user';
            $address->country_id = $request->country;
            $address->division_id = $request->division;
            $address->city_id = $request->city;
            $address->area_id = $request->area;
            $address->address = $request->address;
            $address->zip_code = $request->zip_code;
            $address->save();
        }
    }

}
