<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FirebaseTestNotification;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Utilisateur;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

        protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 

        $this->actingAs(\App\Models\User::factory()->create(), 'sanctum');
    }

    // ===================== testNotificationEnvoieBasique =====================
    /** @test */
    public function testNotificationEnvoieBasique()
    {
        if (!File::exists(base_path('app/firebase/firebase-credentials.json'))) {
            $this->markTestSkipped('Fichier firebase-credentials.json manquant');
        }

        $response = $this->getJson('/api/send-firebase-notification');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Notifications envoyées aux utilisateurs disponibles.',
            ]);
    }

    // ===================== testNotificationEnvoieRenfort =====================
    /** @test */

    public function testNotificationEnvoieRenfort()
    {
        // Skip test if Firebase credentials are missing
        if (!File::exists(base_path('app/firebase/firebase-credentials.json'))) {
            $this->markTestSkipped('Fichier firebase-credentials.json manquant');
        }

        $response = $this->getJson('/api/renfort-notification');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'Notifications envoyées aux utilisateurs disponibles.',
        ]);
    }

    // ===================== testNotificationDepart =====================
    /** @test*/
    public function testNotificationDepart()
    {
        // Skip test if Firebase credentials are missing
        if (!File::exists(base_path('app/firebase/firebase-credentials.json'))) {
            $this->markTestSkipped('Fichier firebase-credentials.json manquant');
        }

        // Payload envoyé à l'API
        $payload = [
            'veh_no' => 2,
            'inter_nom' => 'test',
        ];

        $response = $this->getJson('/api/depart-notification', $payload);

        // Vérifications
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'Notifications envoyées aux utilisateurs disponibles.',
        ]);
    }

    // ===================== sendArriveNotification =====================
    /** @test */
    public function testNotificationArrivee()
    {
        $payload = [
            'veh_no' => 2,
        ];

        $response = $this->getJson('/api/arrive-notification', $payload); // Ajuste la route

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'Notifications envoyées aux utilisateurs disponibles.',
        ]);
    }
}