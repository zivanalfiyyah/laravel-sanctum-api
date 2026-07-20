<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_cannot_access_users_without_token()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_can_get_all_users_with_token()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_show_user_detail()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.email' , $targetUser->email);
    }

    public function test_returns_404_if_user_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users/999');

        $response->assertStatus(404);
    }

    public function test_can_register_new_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Haikal',
            'email' => 'haikal@example.com',
            'password' => 'haikalganteng',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'message', 'access_token', 'token_type']);
    }

    public function test_register_fail_with_duplicate_email()
    {
        User::factory()->create(['email' => 'haikal@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Haikal Lain',
            'email' => 'haikal@example.com',
            'password' => 'haikalganteng',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'haikal@example.com',
            'password' => bcrypt('haikalganteng'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'haikal@example.com',
            'password' => 'haikalganteng',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message', 'access_token', 'token_type']);

    }

    public function test_login_fails_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'haikal@example.com',
            'password' => bcrypt('haikalganteng'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'haikal@example.com',
            'password' => 'haikalSalah',
        ]);

        $response->assertStatus(401);
    }

    public function test_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_can_update_own_profile()
    {
        $user = User::factory()->create(['name' => 'nama lama']);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/users/' . $user->id, [
            'name' => 'nama baru',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'nama baru');
    }

    public function test_can_delete_own_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
