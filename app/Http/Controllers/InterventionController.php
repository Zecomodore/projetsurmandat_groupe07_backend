<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intervention;

class InterventionController extends Controller
{
    public function get_interventions(Request $request){
        /*
        if ($request->user()->name !== 'ChefIntervention') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
            */

        $interventions = Intervention::get_interventions();
        return response()->json($interventions);
    }

    public function get_interventions_dispo(Request $request){
        $interventions = Intervention::get_interventions_dispo();
        return response()->json($interventions);
    }

    public function create_intervention(Request $request){
        if ($request->user()->name !== 'ChefIntervention' && $request->user()->name !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $intervention = Intervention::create_intervention($request);
        return response()->json($intervention);
    }

    public function finish_intervention(Request $request){
        if ($request->user()->name !== 'ChefIntervention') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $intervention = Intervention::finish_intervention($request);
        return response()->json($intervention);
    }

    public function filtrerUrgences(Request $request)
    {
        $filters = [
            'debutDate' => $request->input('debutDate'),
            'finDate' => $request->input('finDate'),
            'debutHeure' => $request->input('debutHeure'),
            'finHeure' => $request->input('finHeure'),
            'typeIntervention' => $request->input('typeIntervention'),
            'enCours' => $request->input('enCours'),
        ];

        $resultats = Intervention::filtrerUrgences($filters);

        return response()->json($resultats, 200);
    }

}
