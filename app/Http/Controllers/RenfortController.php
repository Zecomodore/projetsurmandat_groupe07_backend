<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\User;

class RenfortController extends Controller
{
    public function demandeRenfort(Request $request)
    {
        // Logique pour la demande de renfort ici
        $interventionId = $request->input('intervention_id');

        $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $messaging = $factory->createMessaging();

        $message = CloudMessage::new()
            ->withNotification([
                'title' => 'Demande de renfort!',
                'body' => 'Une demande de renfort a été effectuée pour l\'intervention.',
            ])
            ->withAndroidConfig([
                'priority' => 'high',
            ]);

        $messaging->sendMulticast($message, $fcmTokens);

        return response()->json(['message' => 'Demande de renfort envoyée avec succès']);
    }
}
