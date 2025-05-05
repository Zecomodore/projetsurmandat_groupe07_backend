<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseNotificationService;
use Kreait\Laravel\Firebase\Facades\Firebase;



class NotificationController extends Controller
{
    public function storeToken(Request $request, FirebaseNotificationService $service)
    {
        $request->validate(['token' => 'required']);
        $service->storeToken(auth()->id(), $request->token);
        return response()->json(['status' => 'success']);
    }

    public function sendAlerte(FirebaseNotificationService $service)
    {
        $service->sendToAll('Alerte', 'Nouvelle alerte');
        return response()->json(['status' => 'Notification envoyée']);
    }

    public function sendRenfort(FirebaseNotificationService $service)
    {
        $service->sendToAll('Alerte', 'Demande de renfort');
        return response()->json(['status' => 'Notification envoyée']);
    }
}