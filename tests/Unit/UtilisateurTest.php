<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class UtilisateurTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 
    }
    // ===================== get_utilisateur =====================
    /** @test */
    public function test_get_utilisateur()
    {
        $utilisateur = new Utilisateur;
        $response = $utilisateur->get_utilisteur(5);

        $this->assertEquals('Kilian', $response->first()->uti_prenom);
        $this->assertEquals('Lam', $response->first()->uti_nom);
        $this->assertEquals(5, $response->first()->uti_use_id);
    }

    /** @test */
    public function test_get_utilisateur_error1()
    {
        $utilisateur = new Utilisateur;
        $response = $utilisateur->get_utilisteur(5);

        $this->assertNotEquals('Paul', $response->first()->uti_prenom);
    }

    /** @test */
    public function test_get_utilisateur_error2()
    {
        $utilisateur = new Utilisateur;
        $response = $utilisateur->get_utilisteur(999);

        $this->assertNull($response->first());
    }

    // ===================== utilisateur_indisponible =====================
    /** @test */
    public function test_utilisateur_indisponible()
    {
        $utilisateur = new Utilisateur;
        $uti = $utilisateur->get_utilisteur(5);
        $this->assertTrue(true, $uti->first()->uti_disponible);

        $request = new Request([
            'uti_use_no' => 5,
        ]);
        $response = $utilisateur->utilisateur_indisponible($request);

        $this->assertFalse(false, $response->first()->uti_disponible);
    }

    /** @test */
    public function test_utilisateur_indisponible_error()
    {
        $utilisateur = new Utilisateur;
        $uti = $utilisateur->get_utilisteur(999);
        $this->assertNull($uti->first());

        $request = new Request([
            'uti_use_no' => 999,
        ]);
        $response = $utilisateur->utilisateur_indisponible($request);

        $this->assertNull($response);
    }

    // ===================== envoyer_email =====================
    /** @test */
    public function test_envoyer_email()
    {
        // Crée une requête avec les données nécessaires
        $request = new Request([
            'email' => 'kiliansalut@gmail.com',
            'details' => 'test',
            'subject' => 'test',
        ]);

        $envoyerEmail = new Utilisateur;
        $response = $envoyerEmail->envoie_email($request);

        // Vérifie que la réponse HTTP retourne un code 200 (succès)
        $this->assertEquals(200, $response->status());
    }


    /** @test */
    public function test_envoyer_email_error()
    {
        $request = new Request([
            'email' => 'fauxemail@gmail.com',
            'details' => 'test',
            'subject' => 'test',
        ]);
        try {
            $utilisateur = new Utilisateur;
            $utilisateur->envoie_email($request);
    
            // Si aucune exception n'est levée, le test doit échouer :
            $this->fail('Une exception HttpException aurait dû être levée');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Cet email ne correspond à aucun compte', $e->getMessage());
        }
    }
}
