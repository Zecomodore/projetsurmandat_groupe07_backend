<?php

namespace Tests\Unit;

use Tests\TestCase; // Utiliser la classe TestCase de Laravel
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;

class EnvoyerMailTest extends TestCase
{
    /** @test */
    public function test_envoyer_email()
    {
        // Simule l'envoi d'email pour ne pas envoyer d'emails réels pendant les tests
        Mail::fake(); // sans "::fake" la méthode assertSent n'est pas disponible

        // Crée une requête avec les données nécessaires
        $request = new Request([
            'email' => 'kiliansalut@gmail.com',
            'details' => 'test',
            'subject' => 'test',
        ]);

        // Appel de la méthode envoyerEmail du contrôleur
        $envoyerEmail = new EmailController;
        $response = $envoyerEmail->envoyerEmail($request);

        //erreur pour le moment
        // Vérifie que la réponse HTTP retourne un code 200 (succès)
        $this->assertEquals(200, $response->status());
        
         // Vérifie que l'email a bien été envoyé
         Mail::assertSent(MyMail::class, function ($mail) use ($request) {
            return $mail->hasTo($request->email) &&
                   strpos($mail->subject, 'Code de réinitialisation du mot de passe') !== false;
        });
    }

    /** @test */
    public function test_envoyer_email_error()
    {
        // Simule l'envoi d'email pour ne pas envoyer d'emails réels pendant les tests
        Mail::fake(); // sans "::fake" la méthode assertSent n'est pas disponible

        // Crée une requête avec les données nécessaires
        $request = new Request([
            'email' => 'fauxemail@gmail.com',
            'details' => 'test',
            'subject' => 'test',
        ]);

        // Appel de la méthode envoyerEmail du contrôleur
        $envoyerEmail = new EmailController;
        $response = $envoyerEmail->envoyerEmail($request);

        //erreur pour le moment
        $this->assertEquals(500, $response->status());
    }
}
