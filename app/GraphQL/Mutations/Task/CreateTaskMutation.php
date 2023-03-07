<?php

namespace App\GraphQL\Mutations\Task;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateTaskMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTask',
        'description' => 'Create a task',
    ];

    public function type(): Type
    {
        return GraphQL::type('Task');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['required'],
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
            ],
            'due_date_at' => [
                'name' => 'due_date_at',
                'type' => Type::string(),
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'name.required' => 'Please enter the name',
        ];
    }

    public function resolve($root, $args)
    {
        Log::info('creating task', ['args' => $args]);

        try
        {
            $task = new Task();
            $task->fill($args);
            $task->save();

            Log::info('category created', ['task.id' => $task->id]);
            
            return $task;

        } catch (\Throwable $e) {

            Log::error('error creating task', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}