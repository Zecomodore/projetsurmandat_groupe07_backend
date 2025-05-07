<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;

class VehiculeController extends Controller
{
    //Crud pour admin
    //Read all
    public function get_all_vehicules(){
    $vehicules = Vehicule::all(); // Récupère tous les véhicules
    return response()->json($vehicules, 200);
    }
    

    public function get_vehicule($id){
        $vehicule = Vehicule::get_vehicule($id);
        return response()->json($vehicule);
    }

    public function vehicule_indisponible(Request $request){
        $vehicule = Vehicule::vehicule_indisponible($request);
        return response()->json($vehicule);
    }

    public function vehicule_disponible(Request $request){
        $vehicule = Vehicule::vehicule_disponible($request);
        return response()->json($vehicule);
    }
}
