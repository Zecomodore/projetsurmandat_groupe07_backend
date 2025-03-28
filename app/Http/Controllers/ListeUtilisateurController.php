<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeUtilisateur;

class ListeUtilisateurController extends Controller
{
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
}
