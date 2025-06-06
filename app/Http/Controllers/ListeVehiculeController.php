<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeVehicule;

class ListeVehiculeController extends Controller
{
    

    public function get_vehicules_par_intervention($id, Request $request){
        $vehicules = ListeVehicule::get_vehicules_par_intervention($id);
        if ($vehicules->isEmpty()) {
            return response()->json(['message' => 'Aucun véhicule trouvé pour cette intervention.'], 404);
        }
        return response()->json($vehicules, 200);
    }


    public function get_vehicule_intervention($id,Request $request){
        if ($request->user()->name !== 'ChefIntervention') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $lst_vehicule = ListeVehicule::get_vehicule_intervention($id);
        return response()->json($lst_vehicule);
    }

    public function ajout_vehicule_intervenant(Request $request){
        $lst_vehicule = ListeVehicule::ajout_vehicule_intervenant($request);
        return response()->json($lst_vehicule);
    }

    public function mettre_fin_intervention(Request $request){
        $lst_vehicule = ListeVehicule::mettre_fin_intervention($request);
        return response()->json($lst_vehicule);
    }

    public function mettre_arrive(Request $request){
        $lst_vehicule = ListeVehicule::mettre_arrive($request);
        return response()->json($lst_vehicule);
    }

    public function get_etat_vehicule(Request $request){
        $lst_vehicule = ListeVehicule::get_etat_vehicule($request);
        return response()->json($lst_vehicule);
    }

    public function get_est_en_intervention_vehicule(Request $request){
        $lst_vehicule = ListeVehicule::get_est_en_intervention_vehicule($request);
        return response()->json($lst_vehicule);
    }
}
