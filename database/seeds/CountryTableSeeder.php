<?php

use App\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table("countries")->truncate();
        $clinet = new \GuzzleHttp\Client();
        $result = $clinet->request('GET', 'https://restcountries.eu/rest/v2/all');
        $result = json_decode($result->getBody()->getContents());
        collect($result)->map(function ($country) {
            $latlng = $country->latlng;
            $cn = new Country();
            $cn->name = $country->name;
            $cn->code = $country->alpha2Code;
            $cn->lat = array_key_exists(0, $latlng) ? $latlng[0] : null;
            $cn->long = array_key_exists(1, $latlng) ? $latlng[1] : null;
            $cn->save();
        });
    }
}
