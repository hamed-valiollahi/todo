<?php

namespace App\GraphQL\Queries\Task;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TaskQuery extends Query
{
    protected $attributes = [
        'name' => 'task',
        'description' => 'Get a task',
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
                'type' => Type::int(),
                'rules' => ['required'],
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.required' => 'Please enter the id',
        ];
    }

    public function resolve($root, $args)
    {
        try
        {
            $task = Task::find( $args['id'] );

            if (!$task)
                return null;
            
            return $task;
        } catch (\Throwable $e) {

            Log::error('error showing a task', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
