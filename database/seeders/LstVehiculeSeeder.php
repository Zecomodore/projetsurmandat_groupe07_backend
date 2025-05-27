<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LstVehiculeSeeder extends Seeder
{
    public function run()
    {
        DB::table('lst_vehicule')->insert([
            ['lsv_depart' => Carbon::now()->subHours(2), 'lsv_arrivee' => Carbon::now(),'lsv_present' => false,'lsv_veh_no' => 1, 'lsv_int_no' => 1],
            /*
            ['lsv_temps' => Carbon::now()->subHours(2), 'lsv_present' => true, 'lsv_veh_no' => 1, 'lsv_int_no' => 2],
            ['lsv_temps' => Carbon::now()->subHours(4),'lsv_present' => true, 'lsv_veh_no' => 1, 'lsv_int_no' => 3],*/
        ]);
    }
}
