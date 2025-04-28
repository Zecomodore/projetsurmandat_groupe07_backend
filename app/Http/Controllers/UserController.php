<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Enregistre ou met à jour le token FCM pour l'utilisateur connecté
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user->fcm_token = $request->input('fcm_token');
        $user->save();

        return response()->json(['message' => 'Token FCM enregistré avec succès']);
    }
}

