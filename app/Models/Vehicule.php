<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Vehicule extends Model
{
    use HasFactory;
    protected $fillable = [
        'veh_no', 
        'veh_nom', 
        'veh_disponible',
        'veh_use_id'];
    protected $table = 'vehicule';
    protected $primaryKey = 'veh_no';
    public $timestamps = false;

    public static function get_all_vehicules(){
        $vehicules = Vehicule::all();
        return response()->json($vehicules);
    }
    
    public static function get_vehicule($id){
        $vehicule = Vehicule::where('veh_use_id', $id)->get();
        return $vehicule;
    }

    public static function vehicule_indisponible(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_no)->first(); 

        if ($vehicule) {
            $vehicule->veh_disponible = false;
            $vehicule->save();
            return $vehicule;
        }
    }

    public static function vehicule_disponible(Request $request){
        $vehicule = Vehicule::where('veh_use_id', $request->veh_use_no)->first(); 

        if ($vehicule) {
            $vehicule->veh_disponible = true;
            $vehicule->save();
            return $vehicule;
        }
    }

}
