<?php

namespace App\Http\Controllers;

use App\HomeSlider;
use App\Product;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class HomeSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['homeBannerList', 'homeCategoryListing']]);
    }

    use FileUpload;

    public function homeCategoryListing()
    {
        return DB::table('categories')->orderByRaw('ISNULL(home_serial), home_serial ASC')->where('home_serial', '!=', Null)->get();
    }

    public function homeBannerList()
    {
        $banner = HomeSlider::all()->first();
        if (!$banner) {
            $banner = new HomeSlider();
            $banner->home_banner = '';
            $banner->save();
        }
        return $banner;
    }

    public function homeBanner(Request $request)
    {
        $this->validate($request, [
            'home_banner' => 'required',
        ]);
        $banner = HomeSlider::all()->first();
        if (!$banner) {
            $banner = new HomeSlider();
            $banner->home_banner = '';
            $banner->save();
        }
        $photos = [];
        foreach ($request->home_banner as $photo) {
            if (array_key_exists("path", $photo) && strlen($photo['path']) > 200) {
                $image = $this->saveImagesVue($photo, 'path', 'upload/home/slider/', 920, 350);
                array_push($photos, $image);
            } else {
                foreach (json_decode($banner->home_banner) as $pho) {
                    if (strpos($photo['path'], $pho)) {
                        array_push($photos, $pho);
                    }
                }
            }
        }

        $banner->home_banner = json_encode($photos);
        $banner->save();

        return 'done';
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
        //
    }

    public function destroy($id)
    {
        //
    }
}
