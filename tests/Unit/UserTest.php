<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); 
    }

    // ===================== Connexion =====================
    /** @test */
    public function test_user_fillable_fields_suceed()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
            'code' => '1234AB',
        ];
        $user = User::create($data);

        // tester la connexion avec l'utilisateur précédement créé
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'code' => '1234AB',
        ]);
    }

    /** @test */
    public function test_user_fillable_fields_error()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
            'code' => '1234AB',
        ];
        $user = User::create($data);

        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
            'code' => '99999',
        ]);
    }

    // ===================== vérifier que les données sensible soient dans la propriété $hidden =====================
    /** @test */
    public function test_user_hidden_fields_are_not_visible()
    {
        $user = User::factory()->make([
            'password' => 'secret',
            'remember_token' => 'abc123'
        ]);
        $array = $user->toArray();

        // vérifier que les données soient dans la propriété $hidden
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    // ===================== Vérifier que le mdp soit automatiquement haché =====================
    /** @test */
    public function test_password_is_hashed_when_set()
    {
        $user = User::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'plaintext123',
        ]);

        $this->assertNotEquals('plaintext123', $user->password); // vérifier que le mdp ne soit pas en clair
        $this->assertTrue(\Hash::check('plaintext123', $user->password)); // vérifier quand le mdp haché correspond au mdp d'origine
    }
}
