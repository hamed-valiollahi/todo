<?php

namespace App\GraphQL\Queries\Category;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CategoryQuery extends Query
{
    protected $attributes = [
        'name' => 'category',
        'description' => 'Get a category',
    ];

    public function type(): Type
    {
        return GraphQL::type('Category');
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
            $task = Category::find( $args['id'] );

            if (!$task)
                return null;
            
            return $task;
        } catch (\Throwable $e) {

            Log::error('error showing a category', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
