<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class VehiculeSeeder extends Seeder
{
    public function run()
    {
        DB::table('vehicule')->insert([
            ['veh_nom' => 'Camion de pompiers', 'veh_disponible' => true, 'veh_use_id' => 2],
        ]);
    }
}
