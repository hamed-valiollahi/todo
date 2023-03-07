<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use Validator;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     * path="/categories",
     * summary="List of categories",
     * description="List of categories",
     * operationId="categories_index",
     * tags={"Category"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Categories retrieved.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="array",
     *          @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Personal"),
     *          ),
     *      ),
     *      @OA\Property(property="message", type="string", example="Categories retrieved successfully."),
     *    ),
     * ),
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        
        return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @OA\POST(
     * path="/categories",
     * summary="Create a new category",
     * description="Create a new category",
     * operationId="categories_store",
     * tags={"Category"},
     * security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="string", example="Ryan"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Category created",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Ryan"),
     *      ),
     *      @OA\Property(property="message", type="string", example="Category created successfully."),
     *    ),
     * ),
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        Log::info('creating category', ['input' => $input]);
    
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        try
        {
            $category = Category::create($input);

            Log::info('category created', ['category.id' => $category->id]);
        
            return $this->sendResponse(new CategoryResource($category), 'Category created successfully.', 201);

        } catch (\Throwable $e) {

            Log::error('error creating category', ['input' => $input, 'error' => $e->getMessage()]);

            return $this->sendError('An error has been occured!', null, 500); 
        }
    } 
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @OA\Get(
     * path="/categories/{id}",
     * summary="Get a category",
     * description="Get a category",
     * operationId="categories_show",
     * tags={"Category"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Categories retrieved.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Personal"),
     *      ),
     *      @OA\Property(property="message", type="string", example="Category retrieved successfully."),
     *    ),
     * ),
     * )
     */
    public function show($id): JsonResponse
    {
        $category = Category::find($id);
    
        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }
    
        return $this->sendResponse(new CategoryResource($category), 'Category retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @OA\Put(
     * path="/categories/{id}",
     * summary="Update a category",
     * description="Update a category",
     * operationId="categories_update",
     * tags={"Category"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="string", example="Ryan"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Category created",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Ryan"),
     *      ),
     *      @OA\Property(property="message", type="string", example="Category updated successfully."),
     *    ),
     * ),
     * )
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $input = $request->all();

        Log::info('updating category', ['input' => $input]);
    
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        try
        {
            $category->name = $input['name'];
            $category->save();

            Log::info('category updated', ['category.id' => $category->id]);
        
            return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');

        } catch (\Throwable $e) {

            Log::error('error updating category', ['input' => $input, 'error' => $e->getMessage()]);

            return $this->sendError('An error has been occured!', null, 500); 
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @OA\Delete(
     * path="/categories/{id}",
     * summary="Delete a category",
     * description="Delete a category",
     * operationId="categories_destroy",
     * tags={"Category"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Category created",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object"),
     *      @OA\Property(property="message", type="string", example="Category deleted successfully."),
     *    ),
     * ),
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
    
        return $this->sendResponse([], 'Category deleted successfully.');
    }
}
