<?php

namespace App\GraphQL\Queries\Task;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TasksQuery extends Query
{
    protected $attributes = [
        'name' => 'tasks',
        'description' => 'List of tasks',
    ];

    public function type(): Type
    {
        return Type::listOf( GraphQL::type('Task') );
    }

    public function args(): array
    {
        return [
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
            ],
            'sortbyDueDate' => [
                'name' => 'sortbyDueDate',
                'type' => Type::boolean(),
            ],
        ];
    }
    
    public function resolve($root, array $args)
    {
        try
        {
            $task = Task::query();

            if (isset($args['category_id'])) {
                $task->where('category_id' , $args['category_id'])->get();
            }

            if (isset($args['sortbyDueDate']) && $args['sortbyDueDate']) {
                $task->orderByRaw('-due_date_at DESC');
            }

            return $task->with('category')->get();
            
        } catch (\Throwable $e) {

            Log::error('error showing tasks', ['error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
