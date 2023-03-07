<?php

namespace App\GraphQL\Mutations\Task;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Mutation;

class DeleteTaskMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTask',
        'description' => 'Delete a task',
    ];
    
    public function type(): Type
    {
        return Type::boolean();
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
        Log::info('deleting task', ['args' => $args]);

        try
        {
            $task = Task::find( $args['id'] );

            if (!$task)
            {
                Log::info('task not found', ['task.id' => $args['id']]);

                return null;
            }
            
            if ($task->delete())
            {
                Log::info('task deleted', ['task.id' => $task->id]);
                
                return true;
            }

        } catch (\Throwable $e) {

            Log::error('error deleting task', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
