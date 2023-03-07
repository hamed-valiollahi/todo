<?php

namespace App\GraphQL\Queries\Category;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use GraphQL\Type\Definition\Type;
use GraphQL\Error\Error;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CategoriesQuery extends Query
{
    protected $attributes = [
        'name' => 'categories',
        'description' => 'List of categories',
    ];

    public function type(): Type
    {
        return Type::listOf( GraphQL::type('Category') );
    }
    
    public function resolve($root, array $args)
    {
        try
        {
            return Category::all();

        } catch (\Throwable $e) {

            Log::error('error showing categories', ['error' => $e->getMessage()]);

            return new Error('An error has been occured!');
        }
    }
}
