<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;

class VehiculeController extends Controller
{
    //Read all
    public function get_all_vehicules(){
        $vehicules = Vehicule::get_all_vehicules();
        return response()->json($vehicules, 200);
    }

    public function deleteVehicule($id){
        // Appeler la méthode du modèle pour supprimer le véhicule
        return Vehicule::deleteVehicule($id);
    }
    
    public function modifierVehicule(Request $request, $id){
        // Appeler la méthode du modèle pour modifier le véhicule
        return Vehicule::modifierVehicule($request, $id);
    }

    public function creerVehicule(Request $request)
    {
        return Vehicule::creerVehicule($request);
    }

    public function get_vehicule_admin($id){
        $vehicule = Vehicule::get_vehicule_admin($id);
    
        if (!$vehicule) {
            return response()->json(['message' => 'Véhicule non trouvé'], 404);
        }
    
        return response()->json($vehicule, 200);
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

    public function filtrerVehicules(Request $request){
        $filters = [
            'veh_no' => $request->input('veh_no'),
            'veh_nom' => $request->input('veh_nom'),
            'veh_disponible' => $request->input('veh_disponible'),
        ];

        $resultats = Vehicule::filtrerVehicules($filters);

        return response()->json($resultats, 200);
    }
}
