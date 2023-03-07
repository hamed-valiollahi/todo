<?php

namespace App\GraphQL\Mutations\Task;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Mutation;

class UpdateTaskMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTask',
        'description' => 'Update a task',
    ];

    public function type(): Type
    {
        return GraphQL::type('Task');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
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
            'id.required' => 'Please enter the id',
            'name.required' => 'Please enter the name',
        ];
    }

    public function resolve($root, $args)
    {
        Log::info('updating task', ['args' => $args]);

        try
        {
            $task = Task::find( $args['id'] );

            if (!$task)
            {
                Log::info('task not found', ['task.id' => $args['id']]);

                return null;
            }
            
            $task->fill($args);
            if ($task->save())
            {
                Log::info('task updated', ['task.id' => $task->id]);
                
                return $task;
            }

        } catch (\Throwable $e) {

            Log::error('error updating task', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}