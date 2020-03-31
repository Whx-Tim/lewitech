<?php

use Illuminate\Database\Seeder;

class UmbrellaStationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name_array = [
            '深大','高新园','竹子林'
        ];
        foreach ($name_array as $name) {
            \App\Models\UmbrellaStation::create([
                'name' => $name,
                'amount' => 200,
                'status' => 1
            ]);
        }
    }
}
