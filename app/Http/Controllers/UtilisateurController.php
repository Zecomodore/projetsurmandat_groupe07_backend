<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;

class UtilisateurController extends Controller
{
    //Get all pour admin
    public function get_all_utilisateurs(){
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs, 200);
    }
    

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
        /*
        $envoie_email = new Utilisateur();
        $utilisateur = $envoie_email->envoie_email($request);
        */
        $utilisateur = Utilisateur::envoie_email($request);
        return response()->json($utilisateur);
    }
    
    

    public function code_validation(Request $request){
        //$validation = new Utilisateur();
        $utilisateur = Utilisateur::code_validation($request);
        return response()->json($utilisateur);
    }

    public function changer_mot_de_passe(Request $request) {
        //$changement = new Utilisateur();
        $utilisateur = Utilisateur::changer_mot_de_passe($request);
        return response()->json($utilisateur);
    }
}
