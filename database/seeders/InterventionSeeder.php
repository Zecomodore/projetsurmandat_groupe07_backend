<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InterventionSeeder extends Seeder
{
    public function run()
    {
        DB::table('intervention')->insert([
            ['int_date' => Carbon::now(), 'int_description' => 'Incendie à l’usine'/*, 'int_Adresse' => '10 rue des Pompiers'*/, 'int_en_cours' => true/*, 'int_commentaire' => 'Feu maîtrisé'*/, 'int_heure' => '12:00'],
            ['int_date' => Carbon::now()->subDays(1), 'int_description' => 'Accident de la route'/*, 'int_Adresse' => 'Route nationale 7'*/, 'int_en_cours' => false/*, 'int_commentaire' => ''*/,'int_heure' => '13:00'],
            ['int_date' => Carbon::now()->subDays(2), 'int_description' => 'Sauvetage en montagne'/*, 'int_Adresse' => 'Col du Grand Tour'*/, 'int_en_cours' => false/*, 'int_commentaire' => ''*/,'int_heure' => '14:00'],
        ]);
    }
}
