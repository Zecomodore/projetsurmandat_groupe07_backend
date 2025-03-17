<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListeVehicule extends Model
{
    use HasFactory;
    protected $fillable = ['lsv_no'];
    protected $table = 'lst_vehicule';
    protected $primaryKey = 'lsv_no';

    public static function get_vehicule_intervention($idIntervention){
        $lst_vehicule = DB::table ('lst_vehicule')
        ->join('vehicule', 'lst_vehicule.lsv_veh_no', '=', 'vehicule.veh_no')
        ->where('lst_vehicule.lsv_int_no', $idIntervention)
        ->select('vehicule.veh_nom')
        ->get();
        return $lst_vehicule;
    }
}
