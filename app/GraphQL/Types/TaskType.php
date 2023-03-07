<?php

namespace App\GraphQL\Types;

use App\Models\Task;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TaskType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Task',
        'description' => 'List of tasks and details',
        'model' => Task::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Id of a particular task',
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The Name of the task',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the task',
            ],
            'due_date_at' => [
                'type' => Type::string(),
                'description' => 'The due date of the task',
            ],
            'category' => [
                'type' => GraphQL::type('Category'),
                'description' => 'The category of the task',
            ],
        ];
    }
}
