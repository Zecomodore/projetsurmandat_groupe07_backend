<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LstUtilisateurSeeder extends Seeder
{
    public function run()
    {
        DB::table('lst_utilisateur')->insert([
            ['lsu_present' => false,'lsu_uti_no' => 1, 'lsu_int_no' => 1],
            ['lsu_present' => false,'lsu_uti_no' => 2, 'lsu_int_no' => 2],
            ['lsu_present' => false,'lsu_uti_no' => 2, 'lsu_int_no' => 3],
        ]);
    }
}
