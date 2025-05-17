<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;





class FirebaseTestController extends Controller
{
    public function test()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));

            $auth = $factory->createAuth();

            return response()->json([
                'status' => 'Connexion réussie',
                'message' => 'Les identifiants Firebase sont valides.',
            ]);
        } catch (FirebaseException $e) {
            return response()->json([
                'status' => 'Erreur Firebase',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Erreur générale',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    public function sendNotification()
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));
            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('topic', 'interventions')
                ->withNotification(Notification::create('Nouvelle intervention', 'Une nouvelle intervention vient d’être ajoutée.'));

            $messaging->send($message);

            return response()->json([
                'status' => 'Notification envoyée',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Erreur',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
       */ 
    
    public function sendNotification()
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));
            $messaging = $factory->createMessaging();
            /*
            $utilisateurs = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->select('users.fcm_token')
                ->distinct()
                ->get();
                */
            $usersDispo = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            //Log::info('🎯 Tokens utilisateur :', ['tokens' => $usersDispo]);

            $vehiculesDispo = DB::table('users')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            //Log::info('🎯 Tokens véhicule :', ['tokens' => $vehiculesDispo]);

            foreach ($vehiculesDispo as $tokenVehicule) {
                Log::info('🎯 Token véhicule ajouté :', [$tokenVehicule]);
                $usersDispo[] = $tokenVehicule; 
            }

            //Log::info('🎯 Tokens sélectionnés :', ['tokens' => $usersDispo]);

            foreach ($usersDispo as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create(
                        'Nouvelle intervention',
                        'Une nouvelle intervention vient d’être ajoutée.'
                    ));

                $messaging->send($message);
                //Log::info("✅ Notification envoyée à : $token");
            } catch (\Throwable $e) {
                //Log::error("❌ Erreur envoi notification FCM ($token) : " . $e->getMessage());
            }
        }



            return response()->json(['status' => 'Notifications envoyées aux utilisateurs disponibles.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Erreur', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendRenfortNotification()
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));
            $messaging = $factory->createMessaging();
            /*
            $utilisateurs = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->select('users.fcm_token')
                ->distinct()
                ->get();
                */
            $usersDispo = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            //Log::info('🎯 Tokens utilisateur :', ['tokens' => $usersDispo]);

            $vehiculesDispo = DB::table('users')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            //Log::info('🎯 Tokens véhicule :', ['tokens' => $vehiculesDispo]);

            foreach ($vehiculesDispo as $tokenVehicule) {
                Log::info('🎯 Token véhicule ajouté :', [$tokenVehicule]);
                $usersDispo[] = $tokenVehicule; 
            }

            //Log::info('🎯 Tokens sélectionnés :', ['tokens' => $usersDispo]);

            foreach ($usersDispo as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create(
                        'Demande de renfort',
                        'Renfort demandé pour une intervention.'
                    ));

                $messaging->send($message);
                //Log::info("✅ Notification envoyée à : $token");
            } catch (\Throwable $e) {
                //Log::error("❌ Erreur envoi notification FCM ($token) : " . $e->getMessage());
            }
        }



            return response()->json(['status' => 'Notifications envoyées aux utilisateurs disponibles.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Erreur', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendDepartNotification(Request $request)
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));
            $messaging = $factory->createMessaging();
            /*
            $utilisateurs = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->select('users.fcm_token')
                ->distinct()
                ->get();
                */
            $usersDispo = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('users.name', '=', 'ChefIntervention')
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            $vehicule = DB::table('users')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('users.id', '=', $request->veh_no)
                ->select('vehicule.veh_nom')
                ->first();
            

            //Log::info('🎯 Tokens utilisateur  depart :', ['tokens' => $usersDispo]);

            //Log::info('🎯 Tovehicule :', [$vehicule]);

            foreach ($usersDispo as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create(
                        'Départ Vehicule',
                        'Départ véhicule ' . $vehicule->veh_nom . ', Intervention : ' . $request->inter_nom . '.'
                    ));

                $messaging->send($message);
                //Log::info("✅ Notification envoyée à : $token");
            } catch (\Throwable $e) {
                //Log::error("❌ Erreur envoi notification FCM ($token) : " . $e->getMessage());
            }
        }



            return response()->json(['status' => 'Notifications envoyées aux utilisateurs disponibles.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Erreur', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendArriveNotification(Request $request)
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('app/firebase/firebase-credentials.json'));
            $messaging = $factory->createMessaging();
            /*
            $utilisateurs = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('vehicule.veh_disponible', true)
                ->whereNotNull('users.fcm_token')
                ->select('users.fcm_token')
                ->distinct()
                ->get();
                */
            $usersDispo = DB::table('users')
                ->join('utilisateur', 'users.id', '=', 'utilisateur.uti_use_id')
                ->where('utilisateur.uti_disponible', true)
                ->where('users.name', 'ChefIntervention')
                ->whereNotNull('users.fcm_token')
                ->pluck('users.fcm_token')
                ->toArray();

            //Log::info('🎯 Tokens utilisateur :', ['tokens' => $usersDispo]);

            $vehicule = DB::table('users')
                ->join('vehicule', 'users.id', '=', 'vehicule.veh_use_id')
                ->where('users.id', '=', $request->veh_no)
                ->select('vehicule.veh_nom')
                ->first();

            
            //Log::info('🎯 Tokens sélectionnés :', ['tokens' => $usersDispo]);

            foreach ($usersDispo as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create(
                        'Vehicule arrivé',
                        'Le véhicule ' . $vehicule->veh_nom .' est arrivé !'
                    ));

                $messaging->send($message);
                //Log::info("✅ Notification envoyée à : $token");
            } catch (\Throwable $e) {
                //Log::error("❌ Erreur envoi notification FCM ($token) : " . $e->getMessage());
            }
        }



            return response()->json(['status' => 'Notifications envoyées aux utilisateurs disponibles.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Erreur', 'error' => $e->getMessage()], 500);
        }
    }
       

}
