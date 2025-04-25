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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/interventions', [InterventionController::class, 'get_interventions']);
    Route::post('/interventions', [InterventionController::class, 'create_intervention']);
    Route::put('/interventions', [InterventionController::class, 'finish_intervention']);
    Route::get('/interventions/utilisateur/{id}', [ListeUtilisateurController::class, 'get_personne_intervenant']);
    Route::get('/interventions/vehicule/{id}', [ListeVehiculeController::class, 'get_vehicule_intervention']);
    Route::get('/utilisateur/info/{id}',[UtilisateurController::class, 'get_utilisateur']);
    Route::get('/vehicule/info/{id}',[VehiculeController::class, 'get_vehicule']);
    Route::put('/vehicule/indisponible', [VehiculeController::class, 'vehicule_indisponible']);
    Route::put('/vehicule/disponible', [VehiculeController::class, 'vehicule_disponible']);
    Route::put('/utilisateur/indisponible', [UtilisateurController::class, 'utilisateur_indisponible']);
    Route::put('/utilisateur/disponible', [UtilisateurController::class, 'utilisateur_disponible']);
    Route::post('/interventions/ajout/pompier', [ListeUtilisateurController::class, 'ajout_personne_intervenant']);
    Route::delete('/interventions/supprimer/pompier', [ListeUtilisateurController::class, 'suprimer_intervention']);
    Route::get('/interventions/etat/pompier', [ListeUtilisateurController::class, 'get_etat_personne']);
    Route::post('/changer', [UtilisateurController::class, 'changer_mot_de_passe']);
    Route::post('/interventions/ajout/vehicule', [ListeVehiculeController::class, 'ajout_vehicule_intervenant']);
    Route::put('/interventions/supprimer/vehicule', [ListeVehiculeController::class, 'mettre_fin_intervention']);
    Route::put('/interventions/arrive/vehicule', [ListeVehiculeController::class, 'mettre_arrive']);
    Route::get('/interventions/etat/vehicule', [ListeVehiculeController::class, 'get_etat_vehicule']);
    Route::get('/lstvhicule/etat', [ListeVehiculeController::class, 'get_est_en_intervention_vehicule']);
});

Route::post('/envoie', [UtilisateurController::class, 'envoie_email']);
Route::post('/validation', [UtilisateurController::class, 'code_validation']);





//Route::get('/interventions', [InterventionController::class, 'get_interventions']);
