<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            UtilisateurSeeder::class,
            VehiculeSeeder::class,
            InterventionSeeder::class,
            LstUtilisateurSeeder::class,
            LstVehiculeSeeder::class,
        ]);
    }
}
