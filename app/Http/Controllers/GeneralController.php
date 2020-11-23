<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use Illuminate\Http\Request;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\File;

class GeneralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index']]);
    }

    use FileUpload;

    public function index()
    {
        $general = GeneralSetting::all()->first();
        if ($general) {
            return $general;
        } else {
            $general = new GeneralSetting();
            $general->id = 1;
            $general->save();
            $general = GeneralSetting::all()->first();
            return $general;
        }
    }

    public function logo()
    {
        $general = GeneralSetting::all()->first();
        if ($general) {
            return $general;
        } else {
            $general = new GeneralSetting();
            $general->id = 1;
            $general->save();
            $general = GeneralSetting::all()->first();
            return $general;
        }
    }

    public function logoUpload(Request $request, $id)
    {
        $general = GeneralSetting::findOrFail($id);

        if (count($request->logo) > 0) {
            if (array_key_exists("path", $request->logo[0])) {
                File::delete(public_path($general->logo));
                $logo = $this->saveImagesVue($request->logo[0], 'path', 'upload/general/', 500, 200);
                $general->logo = $logo;
            }
        }

        if (count($request->admin_logo) > 0) {
            if (array_key_exists("path", $request->admin_logo[0])) {
                File::delete(public_path($general->admin_logo));
                $logo = $this->saveImagesVue($request->admin_logo[0], 'path', 'upload/general/', 500, 200);
                $general->admin_logo = $logo;
            }
        }

        if (count($request->admin_login_background) > 0) {
            if (array_key_exists("path", $request->admin_login_background[0])) {
                File::delete(public_path($general->admin_login_background));
                $logo = $this->saveImagesVue($request->admin_login_background[0], 'path', 'upload/general/', 1000, 1000);
                $general->admin_login_background = $logo;
            }
        }

        if (count($request->favicon) > 0) {
            if (array_key_exists("path", $request->favicon[0])) {
                File::delete(public_path($general->favicon));
                $logo = $this->saveImagesVue($request->favicon[0], 'path', 'upload/general/', 100, 100);
                $general->favicon = $logo;
            }
        }

        $general->save();

        return $request->all();
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $general = GeneralSetting::findOrFail($id);
        $general->site_name = $request->site_name;
        $general->address = $request->address;
        $general->phone = $request->phone;
        $general->email = $request->email;
        $general->facebook = $request->facebook;
        $general->instagram = $request->instagram;
        $general->twitter = $request->twitter;
        $general->youtube = $request->youtube;
        $general->google_plus = $request->google_plus;
        $general->footer_text = $request->footer_text;
        $general->save();
    }

    public function destroy($id)
    {
        //
    }
}
