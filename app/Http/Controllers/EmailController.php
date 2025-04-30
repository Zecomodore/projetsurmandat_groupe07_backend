<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;

class EmailController extends Controller
{
    public static function envoyerEmail(Request $request)
    {
        $email = $request->email;
        $details = 'Voici le mail pour réinitialiser le mot de passe --- code de vérification : ' . $request->code;
        $subject = 'Code de réinitialisation du mot de passe';

        $response = Mail::to($email)->send(new MyMail($details, $subject));

        return response()->json(['message' => 'Email envoyé avec succès'], 200);
        //return $response;
    }
}

