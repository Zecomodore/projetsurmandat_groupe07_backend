<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


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
}
