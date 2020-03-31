<?php

use Illuminate\Database\Seeder;

class UmbrellasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($j = 0; $j < 1100; $j++) {
            \App\Models\Umbrella::create([
                'status' => 0,
                'station_id' => 0
            ]);
        }
    }
}
