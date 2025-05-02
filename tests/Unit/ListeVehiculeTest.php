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

}
