<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Intervention;
use Illuminate\Support\Facades\Auth;

class InterventionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crée un utilisateur simulé avec le nom ChefIntervention
        $this->user = User::factory()->create([
            'name' => 'ChefIntervention',
        ]);
    }

        /** @test */
    public function test_get_interventions()
    {
        $getIntervention = new InterventionController;
        $response = $getIntervention->get_interventions();
    }
}
