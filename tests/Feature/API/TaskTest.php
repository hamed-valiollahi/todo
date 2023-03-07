<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;

class TaskTest extends TestCase
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
     * Test task created successfully
     */
    public function test_task_created_successfully(): void
    {
        $this->autheitcate_user();

        $data = [
            "name" => "Susan Wojcicki",
        ];

        $this->json('POST', 'api/tasks', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "name",
                    "description",
                    "due_date_at",
                ],
                "message"
            ]);
    }

    /**
     * Test task listed successfully
     */
    public function test_task_listed_successfully(): void
    {
        $this->autheitcate_user();

        Category::factory()->create([
            "id" => 1,
            "name" => "Personal",
        ]);

        Task::factory()->create([
            "id" => 1,
            "name" => "Spring cleaning",
            "description" => "Get rid of unnecessary items!",
            "due_date_at" => "2023-03-16 19:14:50",
            "category_id" => 1,
        ]);

        Task::factory()->create([
            "id" => 2,
            'name' => 'Find an apartment',
            'description' => null,
            'due_date_at' => "2023-03-16 19:14:01",
            'category_id' => null,
        ]);

        $this->json('GET', 'api/tasks', ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    [
                        "id" => 1,
                        "name" => "Spring cleaning",
                        "description" => "Get rid of unnecessary items!",
                        "due_date_at" => "2023-03-16 19:14:50",
                        "category" => [
                            "id" => 1,
                            "name" => "Personal"
                        ],
                    ],
                    [
                        "id" => 2,
                        'name' => 'Find an apartment',
                        'description' => null,
                        'due_date_at' => "2023-03-16 19:14:01",
                        'category' => null,
                    ]
                ],
                "message" => "Tasks retrieved successfully."
            ]);
    }

    /**
     * Test retrieve task successfully
     */
    public function test_retrieve_task_successfully(): void
    {
        $this->autheitcate_user();

        Category::factory()->create([
            "id" => 1,
            "name" => "Personal",
        ]);

        $task = Task::factory()->create([
            "id" => 1,
            "name" => "Spring cleaning",
            "description" => "Get rid of unnecessary items!",
            "due_date_at" => "2023-03-16 19:14:50",
            "category_id" => 1,
        ]);

        $this->json('GET', 'api/tasks/' . $task->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "id" => 1,
                    "name" => "Spring cleaning",
                    "description" => "Get rid of unnecessary items!",
                    "due_date_at" => "2023-03-16 19:14:50",
                    "category" => [
                        "id" => 1,
                        "name" => "Personal",
                    ],
                ],
                "message" => "Task retrieved successfully."
            ]);
    }

    /**
     * Test task updated successfully
     */
    public function test_task_updated_successfully(): void
    {
        $this->autheitcate_user();

        Category::factory()->create([
            "id" => 1,
            "name" => "Personal",
        ]);

        $task = Task::factory()->create([
            "id" => 2,
            "name" => "Spring cleaning",
            "description" => "Get rid of unnecessary items!",
            "due_date_at" => "2023-03-16 19:14:50",
            "category_id" => 1,
        ]);

        $payload = [
            "name" => "Spring cleaning 2",
            "description" => "Get rid of unnecessary items! 2",
            "due_date_at" => "2023-03-16 19:14:52",
        ];

        $this->json('PUT', 'api/tasks/' . $task->id , $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "id" => 2,
                    "name" => "Spring cleaning 2",
                    "description" => "Get rid of unnecessary items! 2",
                    "due_date_at" => "2023-03-16 19:14:52",
                    "category" => [
                        "id" => 1,
                        "name" => "Personal"
                    ]
                ],
                "message" => "Task updated successfully."
            ]);
    }

    /**
     * Test delete a task
     */
    public function test_delete_task(): void
    {
        $this->autheitcate_user();

        Category::factory()->create([
            "id" => 1,
            "name" => "Personal",
        ]);

        $task = Task::factory()->create([
            "id" => 2,
            "name" => "Spring cleaning",
            "description" => "Get rid of unnecessary items!",
            "due_date_at" => "2023-03-16 19:14:50",
            "category_id" => 1,
        ]);

        $this->json('DELETE', 'api/tasks/' . $task->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200);
    }
}