<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\User;
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
        
        // Utilisateur fictif et temporaire pour les tests de la méthode changement_mdp
        $this->user = User::factory()->create([
            'password' => bcrypt('ancienmdp'),
        ]);
    }
    // ===================== get_utilisateur =====================
    /** @test */
    public function test_get_utilisateur_succes()
    {
        $response = Utilisateur::get_utilisteur(5);

        $this->assertEquals('Kilian', $response->first()->uti_prenom);
        $this->assertEquals('Lam', $response->first()->uti_nom);
        $this->assertEquals(5, $response->first()->uti_use_id);
    }

    /** @test */
    public function test_get_utilisateur_error1()
    {
        $response = Utilisateur::get_utilisteur(5);

        $this->assertNotEquals('Paul', $response->first()->uti_prenom);
    }

    /** @test */
    public function test_get_utilisateur_error2()
    {
        $response = Utilisateur::get_utilisteur(999);

        $this->assertNull($response->first());
    }

    // ===================== utilisateur_indisponible =====================
    /** @test */
    public function test_utilisateur_indisponible_succes()
    {
        $uti = Utilisateur::get_utilisteur(5);
        $this->assertEquals(1, $uti->first()->uti_disponible);

        $request = new Request([
            'uti_use_no' => 5, 
        ]);
        $response = Utilisateur::utilisateur_indisponible($request);
        $this->assertEquals('Kilian' , $response->uti_prenom);
        $this->assertEquals(0 , $response->uti_disponible);
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

    // ===================== utilisateur_disponible =====================
    /** @test */
    public function test_utilisateur_disponible_succes()
    {
        $req = new Request([
            'uti_use_no' => 5, 
        ]);
        $uti = Utilisateur::utilisateur_indisponible($req);
        $this->assertEquals(0, $uti->uti_disponible);

        $request = new Request([
            'uti_use_no' => 5, 
        ]);
        $response = Utilisateur::utilisateur_disponible($request);
        $this->assertEquals('Kilian' , $response->uti_prenom);
        $this->assertEquals(1 , $response->uti_disponible);
    }

    /** @test */
    public function test_utilisateur_disponible_error()
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

    // ===================== code_validation =====================
    /** @test */
    public function test_code_validation_succes()
    {
        // Création d'un utilisateur avec un code (temporairement pour le test)
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'code' => '1234KL',
            'password' => bcrypt('ancienMotDePasse'),
        ]);

        // Création de la requête avec le bon code
        $request = new Request([
            'code' => '1234KL'
        ]);

        // Appel de la méthode
        $response = Utilisateur::code_validation($request);

        // Vérifie que la réponse contient un token (ou autre indicateur d'authentification)
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('token', $response->getData(true));

        // Vérifie que le code est null dans la BDD
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'code' => null
        ]);
    }

    /** @test */
    public function test_code_validation_error()
    {
        // Aucun utilisateur avec ce code
        $request = new Request([
            'code' => 'code_incorrect'
        ]);

        // Appel de la méthode
        $response = Utilisateur::code_validation($request);

        $this->assertEquals(403, $response->status());
        $this->assertEquals(['error' => 'Code incorrect'], $response->getData(true));
    }

    // ===================== changer_mot_de_passe =====================
    /** @test */
    public function test_changement_mdp_succes()
    {
        $request = Request::create('/changer-mdp', 'POST', [
            'password' => 'nouveau123',
            'password_confirmation' => 'nouveau123',
        ]);
        $request->setUserResolver(fn () => $this->user);

        $response = Utilisateur::changer_mot_de_passe($request);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Mot de passe modifié avec succès', $response->getData(true)['message']);
        $this->assertTrue(\Hash::check('nouveau123', $this->user->fresh()->password));
    }

    
    /** @test */
    public function test_changement_mdp_vide()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Le mot de passe ne peut pas être vide');

        $request = Request::create('/changer-mdp', 'POST', [
            'password' => '',
            'password_confirmation' => '',
        ]);
        $request->setUserResolver(fn () => $this->user);

        Utilisateur::changer_mot_de_passe($request);
    }

    
    /** @test */
    public function test_changement_mdp_incorrecte()
    {
        $request = Request::create('/changer-mdp', 'POST', [
            'password' => 'abc12345',
            'password_confirmation' => 'different123',
        ]);
        $request->setUserResolver(fn () => $this->user);

        $response = Utilisateur::changer_mot_de_passe($request);

        $this->assertEquals(400, $response->status());
        $this->assertEquals('Les mots de passe ne correspondent pas', $response->getData(true)['error']);
    }

    // ===================== generateVerificationCode =====================
    /** @test */
    public function test_generate_verification_code_digits()
    {
        $code = Utilisateur::generateVerificationCode();
        
        // Vérifie que les 4 premiers caractères sont des chiffres
        $digits = substr($code, 0, 4);
        $this->assertGreaterThanOrEqual(1000, (int)$digits);
        $this->assertLessThanOrEqual(9999, (int)$digits);
    }

    /** @test */
    public function test_generate_verification_code_letters()
    {
        $code = Utilisateur::generateVerificationCode();
        
        // Vérifie que les 2 derniers caractères sont des lettres majuscules
        $letters = substr($code, 4, 2);
        $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $letters);
    }

    /** @test */
    public function test_generate_verification_code_uniqueness()
    {
        $code1 = Utilisateur::generateVerificationCode();
        $code2 = Utilisateur::generateVerificationCode();
        
        // Vérifie que les deux codes générés sont différents
        $this->assertNotEquals($code1, $code2);
    }


    // ===================== envoyer_email =====================
    /** @test */
    public function test_envoyer_email_succes()
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
