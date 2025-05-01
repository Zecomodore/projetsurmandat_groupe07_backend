<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UtilisateurSeeder extends Seeder
{
    public function run()
    {
        DB::table('utilisateur')->insert([
            ['uti_nom' => 'Dupont', 'uti_prenom' => 'Jean', 'uti_disponible' => true, 'uti_use_id' => 1],
            ['uti_nom' => 'Martin', 'uti_prenom' => 'Paul', 'uti_disponible' => true, 'uti_use_id' => 3],
            ['uti_nom' => 'Barros', 'uti_prenom' => 'bryan', 'uti_disponible' => true, 'uti_use_id' => 4],
            ['uti_nom' => 'Lam', 'uti_prenom' => 'Kilian', 'uti_disponible' => true, 'uti_use_id' => 5],
            ['uti_nom' => 'admin', 'uti_prenom' => 'admin', 'uti_disponible' => true, 'uti_use_id' => 6],
        ]);
    }
}
