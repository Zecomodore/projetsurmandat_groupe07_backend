<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterventionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 
    }

    // ===================== get_interventions =====================
    /** @test */
    public function test_get_interventions_succes()
    {
        $response = Intervention::get_interventions();

        $this->assertEquals(Carbon::now(), $response->first()->int_date);
        $this->assertEquals('Incendie à l’usine', $response->first()->int_description);
        $this->assertEquals(1, $response->first()->int_en_cours);
        $this->assertNotNull($response);
    }

    // ===================== create_intervention =====================
    /** @test */
    public function test_create_intervention_succes()
    {
        $request = new Request([
            'int_description' => 'Test intervention',
            'int_Adresse' => '10 rue des Pompiers',
            'int_commentaire' => 'Feu maîtrisé',
        ]);

        $response = Intervention::create_intervention($request);

        $this->assertEquals('Test intervention', $response->int_description);
        $this->assertEquals(true, $response->int_en_cours);
        $this->assertNotNull($response);
    }

    /** @test */
    public function test_create_intervention_error()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('The int description field is required.');
        
        $request = new Request([
            'int_description' => '', // Champ requis
            'int_Adresse' => '10 rue des Pompiers',
            'int_commentaire' => 'Feu maîtrisé',
        ]);

        $response = Intervention::create_intervention($request);

        $this->assertEquals(422, $response->status());
        $this->assertEquals('The int description field is required.', json_decode($response->getContent())->message);
    }

    // ===================== finish_intervention =====================
    /** @test */
    public function test_finish_intervention_succes()
    {
        $request = new Request([
            'int_no' => 1,
        ]);

        $response = Intervention::finish_intervention($request);

        $this->assertEquals(false, $response->int_en_cours);
        $this->assertNotNull($response);
    }

    /** @test */
    public function test_finish_intervention_error()
    {
        $request = new Request([
            'int_no' => 999, // ID d'intervention qui n'existe pas
        ]);

        $response = Intervention::finish_intervention($request);

        $this->assertEquals(404, $response->status());
        $this->assertEquals('Intervention not found', json_decode($response->getContent())->error);
    }
}
