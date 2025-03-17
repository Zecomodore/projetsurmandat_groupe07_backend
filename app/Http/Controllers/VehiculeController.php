<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;

class VehiculeController extends Controller
{
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
