<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    /**
     * Test required filed for registration api
     */
    public function test_required_fields_for_registration(): void
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Validation Error.',
            'data' => [
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
                'c_password' => ['The c password field is required.'],
            ]
        ]);
    }

    /**
     * Test if password confirmation is correct
     */
    public function test_repeat_password()
    {
        $userData = [
            "name" => "Ryan Doe",
            "email" => "ryan@example.com",
            "password" => "pass123",
            "c_password" => "pass123456"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Validation Error.',
                "data" => [
                    "c_password" => ["The c password field must match password."]
                ]
            ]);
    }

    /**
     * Test if registration is successful
     */
    public function test_successful_registration()
    {
        $userData = [
            "name" => "Ryan Doe",
            "email" => "ryan@example.com",
            "password" => "pass123",
            "c_password" => "pass123"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data" => [
                    'token',
                    'name',
                ],
                "message"
            ]);
    }

    /**
     * Test required filed for login api
     */
    public function testMustEnterEmailAndPassword()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "Sorry, wrong email address or password. Please try again!",
                "data" => [
                    "error" => "Unauthorised"
                ]
            ]);
    }

    /**
     * Test if the user logged in successfully
     */
    public function testSuccessfulLogin()
    {
        User::factory()->create([
           'email' => 'sample@test.com',
           'password' => bcrypt('sample123'),
        ]);


        $loginData = ['email' => 'sample@test.com', 'password' => 'sample123'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data" => [
                    'token',
                    'name',
                ],
                "message"
            ]);

        $this->assertAuthenticated();
    }
}
