<?php

namespace App\GraphQL\Mutations\Category;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Mutation;

class UpdateCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCategory',
        'description' => 'Update a category',
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
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
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
        Log::info('updating category', ['args' => $args]);

        try
        {
            $category = Category::find( $args['id'] );

            if (!$category)
            {
                Log::info('category not found', ['category.id' => $args['id']]);

                return null;
            }
            
            $category->fill($args);
            if ($category->save())
            {
                Log::info('category updated', ['category.id' => $category->id]);
                
                return $category;
            }
            
        } catch (\Throwable $e) {

            Log::error('error updating category', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}