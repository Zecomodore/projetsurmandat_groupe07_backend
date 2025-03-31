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

    public function envoie_email(Request $request){
        $envoie_email = new Utilisateur();
        $utilisateur = $envoie_email->envoie_email($request);
        return response()->json($utilisateur);
    }

    public function code_validation(Request $request){
        $validation = new Utilisateur();
        $utilisateur = $validation->code_validation($request);
        return response()->json($utilisateur);
    }

    public function changer_mot_de_passe(Request $request) {
        $changement = new Utilisateur();
        $utilisateur = $changement->changer_mot_de_passe($request);
        return response()->json($utilisateur);
    }
}
