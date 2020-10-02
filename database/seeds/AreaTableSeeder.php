<?php

use App\City;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table("areas")->truncate();
        foreach (range(1, 500) as $index) {
            DB::table('areas')->insert([
                'city_id' => City::all()->random()->id,
                'name' => $faker->city,
                'zip_code' => $faker->postcode,
                'status' => $faker->randomElement([1, 2, 3]),
            ]);
        }
    }
}
