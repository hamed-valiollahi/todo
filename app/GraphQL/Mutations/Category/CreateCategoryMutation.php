<?php

namespace App\GraphQL\Mutations\Category;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCategory',
        'description' => 'Create a category',
    ];

    public function type(): Type
    {
        return GraphQL::type('Category');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['required'],
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
        Log::info('creating category', ['args' => $args]);

        try
        {
            $category = new Category();
            $category->fill($args);
            $category->save();

            Log::info('category created', ['category.id' => $category->id]);
            
            return $category;
            
        } catch (\Throwable $e) {

            Log::error('error creating category', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}