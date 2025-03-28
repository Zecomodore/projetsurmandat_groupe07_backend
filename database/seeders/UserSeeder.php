<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'ChefIntervention',
            'email' => 'chef1@gmail.com',
            'password' => Hash::make('chef1'),
        ]);

        DB::table('users')->insert([
            'name' => 'Vehicule',
            'email' => 'vehicule1@gmail.com',
            'password' => Hash::make('vehicule1'),
        ]);

        DB::table('users')->insert([
            'name' => 'SapeurPompier',
            'email' => 'sapeur@gmail.com',
            'password' => Hash::make('sapeur1'),
        ]);
    }
}
