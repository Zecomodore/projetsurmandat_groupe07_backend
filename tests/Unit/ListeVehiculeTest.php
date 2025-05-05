<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Models\ListeVehicule;
use App\Models\Vehicule;
use App\Models\User;
use App\Models\Intervention;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




class ListeVehiculeTest extends TestCase
{
     use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 
    }

    // ===================== get_vehicule_intervention =====================
    /** @test */
    public function test_get_vehicule_intervention_sucess() {
        $response = ListeVehicule::get_vehicule_intervention(1);

        $this->assertNotEmpty($response);
        $this->assertCount(1, $response);
        $this->assertEquals("Camion de pompiers", $response[0]->veh_nom);
    }

    /** @test */
    public function test_get_vehicule_intervention_error() {
        $response = ListeVehicule::get_vehicule_intervention(999);

        $this->assertEmpty($response);
        $this->assertCount(0, $response);
    }

    // ===================== ajout_vehicule_intervenant =====================
    /** @test */
    public function test_ajout_vehicule_intervenant_success() {
        // Créer temporairement un user pour le véhicule
        $user = new User();
        $user->name = 'Test User vehicule';
        $user->email = 'test@gmail.com';
        $user->password = bcrypt('111');
        $user->save();

        // Créer temporairement un véhicule et une intervention pour le test
        $vehicule = new Vehicule();
        $vehicule->veh_nom = 'Camion temporaire';
        $vehicule->veh_disponible = 1;
        $vehicule->veh_use_id = $user->id;
        $vehicule->save();

        $request = new Request([
            'veh_use_id' => $user->id,
            'lsv_int_no' => 1,
        ]);

        ListeVehicule::ajout_vehicule_intervenant($request);

        $response = ListeVehicule::get_vehicule_intervention(1);

        $this->assertNotNull($response);
        $this->assertEquals("Camion de pompiers", $response[0]->veh_nom);
        $this->assertEquals("Camion temporaire", $response[1]->veh_nom);
    }

    /** @test */
    public function test_ajout_vehicule_intervenant_error() {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Intervention non trouvée');

        $request = new Request([
            'veh_use_id' => 2, // ID d'un véhicule existant
            'lsv_int_no' => 999,
        ]);
        $response = ListeVehicule::ajout_vehicule_intervenant($request);

        $this->assertNull($response);
    }

    // ===================== mettre_arrive =====================
    /** @test */
    public function test_mettre_arrive_sucess() {
        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 1,
        ]);

        $result = ListeVehicule::mettre_arrive($request);

        // Vérification
        $this->assertNotNull($result->lsv_arrivee);
        $this->assertEquals(true, $result->lsv_present);
    }

    /** @test */
    public function test_mettre_arrive_error() {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Attempt to read property "veh_no" on null');

        $request = new Request([
            'veh_use_id' => 999, // ID d'un véhicule inexistante
            'lsv_int_no' => 1, 
        ]);

        $result = ListeVehicule::mettre_arrive($request);
    }

    /** @test */
    public function test_mettre_arrive_error2() {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Attempt to assign property "lsv_arrivee" on null');

        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 999, // ID d'une intervention inexistante
        ]);

        $result = ListeVehicule::mettre_arrive($request);
    }

    // ===================== mettre_fin_intervention =====================
    /** @test */
    public function test_mettre_fin_intervention_success() {
        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 1,
        ]);

        $result = ListeVehicule::mettre_fin_intervention($request);

        // Vérification
        $this->assertEquals(false, $result->lsv_present);
    }

    /** @test */
    public function test_mettre_fin_intervention_error() {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Attempt to read property "veh_no" on null');

        $request = new Request([
            'veh_use_id' => 999, // ID d'un véhicule inexistante
            'lsv_int_no' => 1, 
        ]);

        $result = ListeVehicule::mettre_fin_intervention($request);
    }

    /** @test */
    public function test_mettre_fin_intervention_error2() {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Attempt to assign property "lsv_present" on null');

        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 999, // ID d'une intervention inexistante
        ]);

        $result = ListeVehicule::mettre_fin_intervention($request);
    }

    // ===================== get_etat_vehicule =====================
    /** @test */
    public function test_get_etat_vehicule_success() {
        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 1,
        ]);

        $result = ListeVehicule::get_etat_vehicule($request);

        // Vérification
        $this->assertTrue($result);
    }
    
    /** @test */
    public function test_get_etat_vehicule_error() {
        $request = new Request([
            'veh_use_id' => 999, // ID d'un véhicule inexistante
            'lsv_int_no' => 1, 
        ]);

        $response = ListeVehicule::get_etat_vehicule($request);

        $this->assertEquals(404, $response->status());
        $this->assertJson($response->getContent());
        $this->assertEquals(['error' => 'Utilisateur non trouvé'], $response->getOriginalContent());
    }

    /** @test */
    public function test_get_etat_vehicule_error2() {
        $request = new Request([
            'veh_use_id' => 2,
            'lsv_int_no' => 999, // ID d'une intervention inexistante
        ]);

        $response = ListeVehicule::get_etat_vehicule($request);
        
        $this->assertFalse($response);
    }

    // ===================== get_est_en_intervention_vehicule =====================
    /** @test */
    public function test_get_est_en_intervention_vehicule_success() {
        $request = new Request([
            'veh_use_id' => 2,
        ]);

        $response = ListeVehicule::get_est_en_intervention_vehicule($request);

        // Vérification
        // ============= /!\ ================
        $this->assertEquals(200, $response->status());
        $this->assertNotNull($response);

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['lsv_int_no']); // ou autre valeur attendue    
    }

    /** @test */
    public function test_get_est_en_intervention_vehicule_error() {
        $request = new Request([
            'veh_use_id' => 999, // ID d'un véhicule inexistante
        ]);

        $response = ListeVehicule::get_est_en_intervention_vehicule($request);

        $this->assertEquals(404, $response->status());
        $this->assertJson($response->getContent());
        $this->assertEquals(['error' => 'Utilisateur non trouvé'], $response->getOriginalContent());
    }
}
