<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\InterventionController;
use App\Http\Controllers\ListeUtilisateurController;
use App\Http\Controllers\ListeVehiculeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;

use App\Http\Controllers\FirebaseTestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/interventions', [InterventionController::class, 'get_interventions']);
    Route::get('/interventions/dispo', [InterventionController::class, 'get_interventions_dispo']);
    Route::post('/interventions', [InterventionController::class, 'create_intervention']);
    Route::put('/interventions', [InterventionController::class, 'finish_intervention']);
    Route::get('/interventions/utilisateur/{id}', [ListeUtilisateurController::class, 'get_personne_intervenant']);
    Route::get('/interventions/vehicule/{id}', [ListeVehiculeController::class, 'get_vehicule_intervention']);
    Route::get('/utilisateur/info/{id}',[UtilisateurController::class, 'get_utilisateur']);
    //Route::get('/utilisateurs', [UtilisateurController::class, 'get_all_utilisateurs']);
    Route::get('/vehicule/info/{id}',[VehiculeController::class, 'get_vehicule']);
    Route::put('/vehicule/indisponible', [VehiculeController::class, 'vehicule_indisponible']);
    Route::put('/vehicule/disponible', [VehiculeController::class, 'vehicule_disponible']);
    Route::put('/utilisateur/indisponible', [UtilisateurController::class, 'utilisateur_indisponible']);
    Route::put('/utilisateur/disponible', [UtilisateurController::class, 'utilisateur_disponible']);
    Route::post('/interventions/ajout/pompier', [ListeUtilisateurController::class, 'ajout_personne_intervenant']);
    Route::put('/interventions/supprimer/pompier', [ListeUtilisateurController::class, 'suprimer_intervention']);
    Route::get('/interventions/etat/pompier', [ListeUtilisateurController::class, 'get_etat_personne']);
    Route::post('/changer', [UtilisateurController::class, 'changer_mot_de_passe']);
    Route::post('/interventions/ajout/vehicule', [ListeVehiculeController::class, 'ajout_vehicule_intervenant']);
    Route::put('/interventions/supprimer/vehicule', [ListeVehiculeController::class, 'mettre_fin_intervention']);
    Route::put('/interventions/arrive/vehicule', [ListeVehiculeController::class, 'mettre_arrive']);
    Route::get('/interventions/etat/vehicule', [ListeVehiculeController::class, 'get_etat_vehicule']);
    Route::get('/lstvhicule/etat', [ListeVehiculeController::class, 'get_est_en_intervention_vehicule']);
    Route::get('/lstutilisateur/etat', [ListeUtilisateurController::class, 'get_est_en_intervention_utilisateur']);
    Route::get('/admin/liste_utilisateur/intervention/{id}', [ListeUtilisateurController::class, 'getPersonnesParIntervention']);
    Route::get('/admin/interventions/vehicules/{id}', [ListeVehiculeController::class, 'get_vehicules_par_intervention']);
    Route::get('/admin/vehicules', [VehiculeController::class, 'get_all_vehicules']);
    Route::get('/admin/utilisateurs', [UtilisateurController::class, 'get_all_utilisateurs']);
    Route::post('/admin/creerutilisateur', [UtilisateurController::class, 'creerUtilisateur']);
    Route::put('/admin/modifierutilisateur/{id}', [UtilisateurController::class, 'update_utilisateur']);
    Route::delete('/admin/supprimerutilisateur/{id}', [UtilisateurController::class, 'delete_utilisateur']);
    Route::post('/admin/creervehicule', [VehiculeController::class, 'creerVehicule']);
    Route::get('/admin/vehicule/{id}', [VehiculeController::class, 'get_vehicule_admin']);
    Route::put('/admin/modifiervehicule/{id}', [VehiculeController::class, 'modifierVehicule']);
    Route::delete('/admin/supprimervehicule/{id}', [VehiculeController::class, 'deleteVehicule']);
    Route::get('/admin/utilisateur/{id}', [UtilisateurController::class, 'get_utilisateur_admin']);
    Route::get('/admin/interventions/filtrerIntervention', [InterventionController::class, 'filtrerUrgences']);
    Route::get('/admin/utilisateurs/filtrerUti', [UtilisateurController::class, 'filtrerUtilisateurs']);
    Route::get('/admin/vehicules/filtrerVehicule', [VehiculeController::class, 'filtrerVehicules']);
    Route::put('/notif/ajout', [UtilisateurController::class, 'ajouter_fcm_token']);
    Route::get('/renfort-notification', [FirebaseTestController::class, 'sendRenfortNotification']);
    Route::get('/send-firebase-notification', [FirebaseTestController::class, 'sendNotification']);
    Route::get('/depart-notification', [FirebaseTestController::class, 'sendDepartNotification']);
    Route::get('/arrive-notification', [FirebaseTestController::class, 'sendArriveNotification']);
    });



Route::post('/envoie', [UtilisateurController::class, 'envoie_email']);
Route::post('/validation', [UtilisateurController::class, 'code_validation']);

//Route::get('/test/fire', [FirebaseTestController::class, 'sendNotificationTest']);







//Route::get('/interventions', [InterventionController::class, 'get_interventions']);
