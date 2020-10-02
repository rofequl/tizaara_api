<?php

use App\Country;
use App\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class DivisionTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        Schema::disableForeignKeyConstraints();
        DB::table("divisions")->truncate();
        $countries = collect(json_decode(
            file_get_contents(
                'https://raw.githubusercontent.com/hiiamrohit/Countries-States-Cities-database/master/countries.json'
            )
        )->countries);
        $states = file_get_contents(
            'https://raw.githubusercontent.com/hiiamrohit/Countries-States-Cities-database/master/states.json'
        );
        $states = collect(json_decode($states)->states);
        $states->map(function ($state) use ($countries, $faker) {
            $countryIDFinder = $countries->first(function ($cn) use ($state) {
                return $state->country_id == $cn->id;
            });
            $existCountry = Country::whereCode($countryIDFinder->sortname)->first();
            Division::updateOrCreate([
                'name' => $state->name
            ], [
                'name' => $state->name,
                'country_id' => optional($existCountry)->id ?? 1,
            ]);
        });
    }
}
