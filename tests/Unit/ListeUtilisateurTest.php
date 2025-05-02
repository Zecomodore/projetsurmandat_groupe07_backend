<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Models\ListeUtilisateur;
use App\Models\Utilisateur;
use App\Models\Intervention;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ListeUtilisateurTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 
    }

    // ===================== get_personne_intervenant =====================
    /** @test */
    public function test_get_personne_intervenant_sucess() {
        $response = ListeUtilisateur::get_personne_intervenant(1);

        $this->assertNotEmpty($response);
        $this->assertCount(1, $response);
        $this->assertEquals("Dupont", $response[0]->uti_nom);
        $this->assertEquals("Jean", $response[0]->uti_prenom);
    }

    // ===================== ajout_personne_intervenant =====================
    /** @test */
    public function test_ajout_personne_intervenant_sucess() {
        $rep = ListeUtilisateur::get_personne_intervenant(1);

        $this->assertNotEmpty($rep);
        $this->assertEquals("Dupont", $rep[0]->uti_nom);
        $this->assertEquals("Jean", $rep[0]->uti_prenom);

        $request = new Request([
            'uti_use_id' => 3, // l'utilisateur 2 est un véhicule -> ne foncitonne pas
            'lsu_int_no' => 1,
        ]);
        ListeUtilisateur::ajout_personne_intervenant($request);

        $response = ListeUtilisateur::get_personne_intervenant(1);

        $this->assertNotEmpty($response);
        $this->assertCount(2, $response);
        $this->assertEquals("Dupont", $response[0]->uti_nom);
        $this->assertEquals("Jean", $response[0]->uti_prenom);
        $this->assertEquals("Martin", $response[1]->uti_nom);
        $this->assertEquals("Paul", $response[1]->uti_prenom);
    }

    /** @test */
    public function test_ajout_personne_intervenant_error() {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Utilisateur non trouvé');

        $request = new Request([
            'uti_use_id' => 999, // ou 2 -> l'utilisateur 2 est un véhicule, donc ne fonctionne pas aussi
            'lsu_int_no' => 1,
        ]);
        ListeUtilisateur::ajout_personne_intervenant($request);
    }

    // ===================== suprimer_intervention =====================
    /** @test */
    public function test_suprimer_intervention_sucess() {
        $request = new Request([
            'uti_use_id' => 1,
            'lsu_int_no' => 1,
        ]);
        ListeUtilisateur::suprimer_intervention($request);

        $response = ListeUtilisateur::get_personne_intervenant(1);

        $this->assertEmpty($response);
        $this->assertCount(0, $response);
    }

    /** @test */
    public function test_suprimer_intervention_error() {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Utilisateur non trouvé');

        $request = new Request([
            'uti_use_id' => 999, // ou 2 -> l'utilisateur 2 est un véhicule, donc ne fonctionne pas aussi
            'lsu_int_no' => 1,
        ]);
        ListeUtilisateur::suprimer_intervention($request);
    }

    // ===================== get_etat_personne =====================
    /** @test */
    public function test_get_etat_personne_sucess() {
        $request = new Request([
            'uti_use_id' => 1,
            'lsu_int_no' => 1,
        ]);
        $response = ListeUtilisateur::get_etat_personne($request);

        $this->assertTrue($response);
    }

    /** @test */
    public function test_get_etat_personne_error() {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Utilisateur non trouvé');

        $request = new Request([
            'uti_use_id' => 999, // ou 2 -> l'utilisateur 2 est un véhicule, donc ne fonctionne pas aussi
            'lsu_int_no' => 1,
        ]);
        ListeUtilisateur::get_etat_personne($request);
    }

    // ===================== get_est_en_intervention_utilisateur =====================
    /** @test */
    public function test_get_est_en_intervention_utilisateur_sucess() {
        $request = new Request([
            'uti_use_id' => 1,
        ]);
        $response = ListeUtilisateur::get_est_en_intervention_utilisateur($request);

        $this->assertTrue($response->getData()->resultat);
    }
    
    /** @test */
    public function test_get_est_en_intervention_utilisateur_error() {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Utilisateur non trouvé');

        $request = new Request([
            'uti_use_id' => 999, // ou 2 -> l'utilisateur 2 est un véhicule, donc ne fonctionne pas aussi
        ]);
        ListeUtilisateur::get_est_en_intervention_utilisateur($request);
    }
}
