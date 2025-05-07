<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeUtilisateur;

class ListeUtilisateurController extends Controller
{

  public function getPersonnesParIntervention($id)
    {
        $utilisateurs = ListeUtilisateur::get_personne_intervenant($id);

        if ($utilisateurs->isEmpty()) {
            return response()->json(['message' => 'Aucun utilisateur trouvé pour cette intervention.'], 404);
        }

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
