<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\User;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $title = $request->input('title');
        $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $messaging = $factory->createMessaging();

        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title, // Titre de l'alerte
                'body' => 'Alerte' 
            ])
            ->withAndroidConfig([
                'priority' => 'high',
            ]);

        $messaging->sendMulticast($message, $fcmTokens);

        return response()->json(['message' => 'Notification envoyée avec succès']);
    }
}
