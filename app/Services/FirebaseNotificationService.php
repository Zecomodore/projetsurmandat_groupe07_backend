<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\FcmToken;

class FirebaseNotificationService
{
    public function sendToAll($title, $body)
    {
        $tokens = FcmToken::pluck('token')->toArray();
        if (empty($tokens)) return;

        $message = CloudMessage::new()->withNotification(
            Notification::create($title, $body)
        );

        Firebase::messaging()->sendMulticast($message, $tokens);
    }

    public function storeToken($userId, $token)
    {
        FcmToken::updateOrCreate(['token' => $token], ['user_id' => $userId]);
    }
}