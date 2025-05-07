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
}
