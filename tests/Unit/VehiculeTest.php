<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;


class VehiculeTest extends TestCase
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

    // ===================== get_vehicule =====================
    /** @test */
    public function test_get_vehicule_sucess()
    {
        $result = Vehicule::get_vehicule(2);

        // Assert : vérifier que le résultat contient bien le véhicule créé
        $this->assertCount(1, $result);
        $this->assertEquals(2, $result->first()->veh_use_id);
        $this->assertEquals('Camion de pompiers', $result->first()->veh_nom);
    }

    /** @test */
    public function test_get_vehicule_error()
    {
        $result = Vehicule::get_vehicule(1); // chef d'intervention

        // Assert : vérifier que le résultat contient bien le véhicule créé
        $this->assertCount(0, $result);
        $this->assertNull($result->first());
    }

    // ===================== test_vehicule_indisponible =====================
    /** @test */
    public function test_vehicule_indisponible_sucess()
    {
        $vehicule = Vehicule::get_vehicule(2);
        $this->assertEquals(1, $vehicule->first()->veh_disponible);

        $request = new Request ([
            'veh_use_no' => 2,
        ]);

        $result = Vehicule::vehicule_indisponible($request);

        $this->assertEquals('Camion de pompiers', $result->veh_nom);
        $this->assertEquals(0, $result->veh_disponible);
    }

    /** @test */
    public function test_vehicule_indisponible_error()
    {
        $request = new Request ([
            'veh_use_no' => 999,
        ]);

        $result = Vehicule::vehicule_indisponible($request);
        $this->assertNull($result);
    }

    // ===================== test_vehicule_disponible =====================
    /** @test */
    public function test_vehicule_disponible_sucess()
    {
        $request = new Request ([
            'veh_use_no' => 2,
        ]);
        $vehicule = Vehicule::vehicule_indisponible($request);
        $this->assertEquals(0, $vehicule->veh_disponible);

        $result = Vehicule::vehicule_disponible($request);
        $this->assertEquals(1, $result->veh_disponible);
    }

    /** @test */
    public function test_vehicule_disponible_error()
    {
        $request = new Request ([
            'veh_use_no' => 999,
        ]);

        $result = Vehicule::vehicule_disponible($request);
        $this->assertNull($result);
    }

    // ===================== creerVehicule =====================
    /** @test */
    public function test_creerVehicule_success() {
        $request = new Request([
            'role' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => '123',
            'veh_nom' => 'Camion de secours',
        ]);

        $response = Vehicule::creerVehicule($request);
        $data = $response->getData(true);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('Camion de secours', $data['vehicule']['veh_nom']);
        $this->assertEquals('Test User', $data['user']['name']);
    }

    /** @test */
    public function test_creerVehicule_mdp_vide() {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Le mot de passe ne peut pas être vide');

        $request = new Request([
            'role' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => '',
            'veh_nom' => 'Camion de secours',
        ]);

        $response = Vehicule::creerVehicule($request);
    }

    // ===================== modifierVehicule =====================
    /** @test */
    public function test_modifierVehicule_success() {
        $request = new Request([
            'veh_nom' => 'Camion de secours modifié',
            'veh_disponible' => 1,
            'email' => 'NewEmail',
        ]);

        $response = Vehicule::modifierVehicule($request, 1);

        $this->assertEquals(200, $response->status());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Véhicule et utilisateur mis à jour avec succès', $data['message']);

        $this->assertEquals('Camion de secours modifié', $data['vehicule']['veh_nom']);
        $this->assertEquals(1, $data['vehicule']['veh_disponible']);
        $this->assertEquals('NewEmail', $data['user']['email']);
    }

    // ===================== deleteVehicule =====================
    /** @test */
    public function test_deleteVehicule_success() {
        $response = Vehicule::deleteVehicule(1);

        $this->assertEquals(200, $response->status());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Véhicule et utilisateur associé supprimés avec succès', $data['message']);
    }

    /** @test */
    public function test_deleteVehicule_error() {
        $response = Vehicule::deleteVehicule(999);

        $this->assertEquals(404, $response->status());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Véhicule non trouvé', $data['message']);
    }
}