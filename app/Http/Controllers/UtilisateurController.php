<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;

class UtilisateurController extends Controller
{
    public function get_utilisateur($id){
        $utilisateur = Utilisateur::get_utilisteur($id);
        return response()->json($utilisateur);
    }

    public function utilisateur_indisponible(Request $request){
        $utilisateur = Utilisateur::utilisateur_indisponible($request);
        return response()->json($utilisateur);
    }

    public function utilisateur_disponible(Request $request){
        $utilisateur = Utilisateur::utilisateur_disponible($request);
        return response()->json($utilisateur);
    }
}
