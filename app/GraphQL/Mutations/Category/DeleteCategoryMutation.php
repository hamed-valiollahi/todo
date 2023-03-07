<?php

namespace App\GraphQL\Mutations\Category;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Mutation;

class DeleteCategoryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCategory',
        'description' => 'Delete a category',
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
        Log::info('deleting category', ['args' => $args]);

        try
        {
            $category = Category::find( $args['id'] );

            if (!$category)
            {
                Log::info('category not found', ['category.id' => $args['id']]);

                return null;
            }

            if ($category->delete())
            {
                Log::info('category deleted', ['category.id' => $category->id]);
                
                return true;
            }

        } catch (\Throwable $e) {

            Log::error('error deleting category', ['args' => $args, 'error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
