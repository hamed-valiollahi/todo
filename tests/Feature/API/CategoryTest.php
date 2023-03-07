<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class CategoryTest extends TestCase
{
    /**
     * Create a sample user
     */
    private function autheitcate_user(): void
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample123'),
         ]);
        $this->actingAs($user);
    }

    /**
     * Test category created successfully
     */
    public function test_category_created_successfully(): void
    {
        $this->autheitcate_user();

        $data = [
            "name" => "Susan Wojcicki",
        ];

        $this->json('POST', 'api/categories', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "name",
                ],
                "message"
            ]);
    }

    /**
     * Test category listed successfully
     */
    public function test_category_listed_successfully(): void
    {
        $this->autheitcate_user();

        Category::factory()->create([
            "id" => 100,
            "name" => "Category 1",
        ]);

        Category::factory()->create([
            "id" => 101,
            "name" => "Category 2",
        ]);

        $this->json('GET', 'api/categories', ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    [
                        "id" => 100,
                        "name" => "Category 1",
                    ],
                    [
                        "id" => 101,
                         "name" => "Category 2",
                    ]
                ],
                "message" => "Categories retrieved successfully."
            ]);
    }

    /**
     * Test retrieve category successfully
     */
    public function test_retrieve_category_successfully(): void
    {
        $this->autheitcate_user();

        $category = Category::factory()->create([
            "id" => 100,
            "name" => "Category 1",
        ]);

        $this->json('GET', 'api/categories/' . $category->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "id" => 100,
                    "name" => "Category 1",
                ],
                "message" => "Category retrieved successfully."
            ]);
    }

    /**
     * Test category updated successfully
     */
    public function test_category_updated_successfully(): void
    {
        $this->autheitcate_user();

        $category = Category::factory()->create([
            "id" => 100,
            "name" => "Category 1",
        ]);

        $payload = [
            "name" => "Updated category name",
        ];

        $this->json('PUT', 'api/categories/' . $category->id , $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "id" => 100,
                    "name" => "Updated category name",
                ],
                "message" => "Category updated successfully."
            ]);
    }

    /**
     * Test delete a category
     */
    public function test_delete_category(): void
    {
        $this->autheitcate_user();

        $category = Category::factory()->create([
            "id" => 100,
            "name" => "Category 1",
        ]);

        $this->json('DELETE', 'api/categories/' . $category->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200);
    }
}