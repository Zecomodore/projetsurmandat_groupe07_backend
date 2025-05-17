<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UtilisateurController extends Controller
{
    public function get_all_utilisateurs(){
        // Appeler la méthode du modèle pour récupérer tous les utilisateurs
        $utilisateurs = Utilisateur::get_all_utilisateurs();
    
        // Retourner la réponse générée par le modèle
        return response()->json($utilisateurs, 200);
        }
        public function get_utilisateur_admin($id)
        {
            $utilisateur = Utilisateur::get_utilisateur_Admin($id);
        
            if (!$utilisateur) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
        
            return response()->json($utilisateur, 200);
        }

    public function get_utilisateur($id){
        $utilisateur = Utilisateur::get_utilisteur($id);
        return response()->json($utilisateur);
    }

    public function delete_utilisateur($id){
        // Appeler la méthode du modèle pour supprimer l'utilisateur
        $result = Utilisateur::delete_utilisateur($id);
    
        // Retourner la réponse générée par le modèle
        return $result;
    }

    public function update_utilisateur(Request $request, $id){
        // Appeler la méthode du modèle pour mettre à jour l'utilisateur
        $result = Utilisateur::update_utilisateur($request, $id);
    
        // Retourner la réponse générée par le modèle
        return $result;
    }

    public function creerUtilisateur(Request $request){
        // Appeler la méthode du modèle pour créer un utilisateur
        return Utilisateur::creerUtilisateur($request);
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

    public function ajouter_fcm_token(Request $request) {
        $utilisateur = Utilisateur::ajouter_token_fcm($request);
        return response()->json($utilisateur);
    }
}
