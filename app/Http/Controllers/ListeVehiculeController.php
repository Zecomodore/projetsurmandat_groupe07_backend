<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeVehicule;

class ListeVehiculeController extends Controller
{
    public function get_vehicule_intervention($id,Request $request){
        if ($request->user()->name !== 'ChefIntervention') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $lst_vehicule = ListeVehicule::get_vehicule_intervention($id);
        return response()->json($lst_vehicule);
    }
}
