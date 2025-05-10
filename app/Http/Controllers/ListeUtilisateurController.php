<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeUtilisateur;

class ListeUtilisateurController extends Controller
{

  public function getPersonnesParIntervention($id)
  {
      // Appeler la méthode du modèle pour récupérer les personnes liées à une intervention
      $utilisateurs = ListeUtilisateur::getPersonnesParIntervention($id);
  
      // Vérifier si des utilisateurs ont été trouvés
      if ($utilisateurs->isEmpty()) {
          return response()->json(['message' => 'Aucun utilisateur trouvé pour cette intervention.'], 404);
      }
  
      // Retourner les utilisateurs trouvés
      return response()->json($utilisateurs, 200);
  }


  public function get_personne_intervenant($id,Request $request){
    if ($request->user()->name !== 'ChefIntervention') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    //$idIntervention = $request->int_no;
    $lst_utilisateur = ListeUtilisateur::get_personne_intervenant($id);
    return response()->json($lst_utilisateur);
  }

  public function ajout_personne_intervenant(Request $request){

    $lst_utilisateur = ListeUtilisateur::ajout_personne_intervenant($request);
    return response()->json($lst_utilisateur);
  }

  public function suprimer_intervention(Request $request){
    $lst_utilisateur = ListeUtilisateur::suprimer_intervention($request);
    return response()->json($lst_utilisateur);
  }

  public function get_etat_personne(Request $request){
    $lst_utilisateur = ListeUtilisateur::get_etat_personne($request);
    return response()->json($lst_utilisateur);
  }

  public function get_est_en_intervention_utilisateur(Request $request){
    $lst_utilisateur = ListeUtilisateur::get_est_en_intervention_utilisateur($request);
    return response()->json($lst_utilisateur);
  }
}
