<?php

namespace App\Http\Controllers\User;

use App\Address;
use App\CompanyBasicInfo;
use App\CompanyDetails;
use App\Http\Controllers\Controller;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FactoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    use FileUpload;

    public function index()
    {
        $factory = CompanyBasicInfo::where('user_id', Auth::user()->id)->first();
        $factory_details = CompanyDetails::where('user_id', Auth::user()->id)->first();

        if (!$factory) {
            return response()->json(['result' => 'Success', 'company' => $factory, 'company_details' => $factory_details], 200);
        }
        $address = Address::find($factory->reg_address_id);

        $factory['country'] = $address->country_id;
        $factory['division'] = $address->division_id;
        $factory['city'] = $address->city_id;
        $factory['area'] = $address->area_id;
        $factory['address'] = $address->address;
        $factory['zip_code'] = $address->zip_code;

        if ($factory->address_type == 2) {
            $address2 = Address::find($factory->ope_address_id);
            $factory['country2'] = $address2->country_id;
            $factory['division2'] = $address2->division_id;
            $factory['city2'] = $address2->city_id;
            $factory['area2'] = $address2->area_id;
            $factory['address2'] = $address2->address;
            $factory['zip_code2'] = $address2->zip_code;
        }

        return response()->json(['result' => 'Success', 'company' => $factory, 'company_details' => $factory_details], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $factory = CompanyBasicInfo::where('user_id', Auth::user()->id)->first();
        $address = Address::where('addressable_id', Auth::user()->id)->where('address_type', 'register')->first();
        $address2 = Address::where('addressable_id', Auth::user()->id)->where('address_type', 'operation')->first();
        if ($request->address_type == 1) {
            if ($address2) {
                $address2->delete();
            }
        }
        if ($request->address_type == 2 && $request->country2 != null) {
            if (!$address2) {
                $address2 = new Address();
                $address2->addressable_id = Auth::user()->id;
                $address2->address_type = "operation";
            }
            $address2->country_id = $request->country2;
            $address2->division_id = $request->division2;
            $address2->city_id = $request->city2;
            $address2->area_id = $request->area2;
            $address2->address = $request->address2;
            $address2->zip_code = $request->zip_code2;
            $address2->save();
        }
        if (!$address) {
            $address = new Address();
            $address->addressable_id = Auth::user()->id;
            $address->address_type = "register";
        }

        $address = $this->saveAddress($address, $request);

        if (!$factory) {
            $factory = new CompanyBasicInfo();
            $factory->user_id = Auth::user()->id;
            $factory->reg_address_id = $address;
        }

        if ($request->address_type == 1) {
            $factory->ope_address_id = null;
        } else {
            $factory->ope_address_id = $address2->id;
        }

        $factory->address_type = $request->address_type;
        $factory->name = $request->name;
        $factory->display_name = $request->display_name;
        $factory->establishment_date = $request->establishment_date;
        $factory->office_space = $request->office_space;
        $factory->website = $request->website;
        $factory->email = $request->email;
        $factory->phone = $request->phone;
        $factory->cell = $request->cell;
        $factory->fax = $request->fax;
        $factory->number_of_employee = $request->number_of_employee;
        $factory->ownership_type = $request->ownership_type;
        $factory->revenue = $request->revenue;
        $factory->main_product = $request->main_product;
        $factory->other_product = $request->other_product;
        $factory->save();


        return 'done';
    }

    public function factoryDetails(Request $request)
    {
        $factory = CompanyDetails::where('user_id', Auth::user()->id)->first();

        if (!$factory) {
            $factory = new CompanyDetails();
            $factory->user_id = Auth::user()->id;
        }

        if ( $request->company_logo != '' && strlen($request->company_logo) > 200) {
            File::delete(public_path($factory->company_logo));
            $factory->company_logo = $this->saveImagesVue2($request->company_logo, 'upload/company/logo/');
        }

        $photos = [];
        foreach ($request->company_photos as $photo) {
            if (array_key_exists("path", $photo) && strlen($photo['path']) > 200) {
                $image = $this->saveImagesVue($photo, 'path', 'upload/company/gallery/', 920, 920);
                array_push($photos, $image);
            } else {
                foreach (json_decode($factory->company_photos) as $pho) {
                    if (strpos($photo['path'], $pho)) {
                        array_push($photos, $pho);
                    }
                }
            }
        }

        $factory->company_photos = json_encode($photos);
        $factory->about_us = $request->about_us;
        $factory->mission = $request->mission;
        $factory->vision = $request->vision;
        $factory->company_video = $request->company_video;
        $factory->facebook_url = $request->facebook_url;
        $factory->save();

        return 'done';
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
