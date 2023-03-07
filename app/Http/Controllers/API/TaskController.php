<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Task;
use Validator;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Log;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     * path="/tasks",
     * summary="List of tasks",
     * description="List of tasks",
     * operationId="tasks_index",
     * tags={"Task"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="category_id", in="query", description="Filter by Category Id", required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(name="sortbyDueDate", in="query", description="Sort by due date", required=false,
     *    @OA\Schema(type="boolean"),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Tasks retrieved.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="array",
     *          @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Spring cleaning"),
     *             @OA\Property(property="description", type="string", example="Get rid of unnecessary items!"),
     *             @OA\Property(property="due_date_at", type="string", example="2023-03-16 19:14:50"),
     *             @OA\Property(property="category", type="object",
     *                @OA\Property(property="id", type="integer", example=1),
     *                @OA\Property(property="name", type="string", example="Personal"),
     *             ),
     *          ),
     *      ),
     *      @OA\Property(property="message", type="string", example="Tasks retrieved successfully."),
     *    ),
     * ),
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'category_id' => 'integer'
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        // Creating the query
        $tasks = Task::query();

        if ($request->input('category_id')) {
            $tasks->where('category_id' , $request->input('category_id'))->get();
        }

        if ($request->input('sortbyDueDate')) {
            $tasks->orderByRaw('-due_date_at DESC');
        }

        $tasks = $tasks->get();

        return $this->sendResponse(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @OA\POST(
     * path="/tasks",
     * summary="Create a new tasks",
     * description="Create a new tasks",
     * operationId="tasks_store",
     * tags={"Task"},
     * security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="string", example="Find an apartment"),
     *       @OA\Property(property="description", type="string", format="string", example="Coquitlam or Burnaby"),
     *       @OA\Property(property="due_date_at", type="string", format="YYYY-MM-DD HH:MM:SS", example="2023-03-21 23:34:43"),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Task created",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Find an apartment"),
     *         @OA\Property(property="description", type="string", example="Coquitlam or Burnaby"),
     *         @OA\Property(property="due_date_at", type="string", example="2023-03-21 23:34:43"),
     *         @OA\Property(property="category_id", type="integer", example=1),
     *      ),
     *      @OA\Property(property="message", type="string", example="Task created successfully."),
     *    ),
     * ),
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        Log::info('creating task', ['input' => $input]);
    
        $validator = Validator::make($input, [
            'name' => 'required',
            'due_date_at' => 'date_format:Y-m-d H:i:s'
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        try {
            $task = Task::create($input);

            Log::info('task created', ['category.id' => $task->id]);
        
            return $this->sendResponse(new TaskResource($task), 'Task created successfully.', 201);

        } catch (\Throwable $e) {

            Log::error('error creating task', ['input' => $input, 'error' => $e->getMessage()]);

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
     * path="/tasks/{id}",
     * summary="Get a tasks",
     * description="Get a tasks",
     * operationId="tasks_show",
     * tags={"Task"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Tasks retrieved.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="Spring cleaning"),
     *          @OA\Property(property="description", type="string", example="Get rid of unnecessary items!"),
     *          @OA\Property(property="due_date_at", type="string", example="2023-03-16 19:14:50"),
     *          @OA\Property(property="category", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Personal"),
     *          ),
     *      ),
     *      @OA\Property(property="message", type="string", example="Task retrieved successfully."),
     *    ),
     * ),
     * )
     */
    public function show($id): JsonResponse
    {
        $tasks = Task::find($id);
    
        if (is_null($tasks)) {
            return $this->sendError('Task not found.');
        }
    
        return $this->sendResponse(new TaskResource($tasks), 'Task retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @OA\Put(
     * path="/tasks/{id}",
     * summary="Update a tasks",
     * description="Update a tasks",
     * operationId="tasks_update",
     * tags={"Task"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="string", example="Find an apartment"),
     *       @OA\Property(property="description", type="string", format="string", example="Coquitlam or Burnaby"),
     *       @OA\Property(property="due_date_at", type="string", format="YYYY-MM-DD HH:MM:SS", example="2023-03-21 23:34:43"),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Task updated",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="Spring cleaning"),
     *          @OA\Property(property="description", type="string", example="Get rid of unnecessary items!"),
     *          @OA\Property(property="due_date_at", type="string", example="2023-03-16 19:14:50"),
     *          @OA\Property(property="category_id", type="integer", example=1),
     *      ),
     *      @OA\Property(property="message", type="string", example="Task updated successfully."),
     *    ),
     * ),
     * )
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $input = $request->all();

        Log::info('updating task', ['input' => $input]);
    
        $validator = Validator::make($input, [
            'name' => 'required',
            'due_date_at' => 'date_format:Y-m-d H:i:s'
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        try
        {
            $task->name = $input['name'];
            $task->description = $input['description'] ?? null;
            $task->due_date_at = $input['due_date_at'] ?? null;
            $task->category_id = $input['category_id'] ?? null;
            $task->save();
        
            return $this->sendResponse(new TaskResource($task), 'Task updated successfully.');

        } catch (\Throwable $e) {

            Log::error('error updating task', ['input' => $input, 'error' => $e->getMessage()]);

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
     * path="/tasks/{id}",
     * summary="Delete a tasks",
     * description="Delete a tasks",
     * operationId="tasks_destroy",
     * tags={"Task"},
     * security={{"bearer_token":{}}},
     * @OA\Parameter(name="id", in="path", description="Id of Item", required=true,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Task deleted",
     *    @OA\JsonContent(
     *      @OA\Property(property="success", type="boolean", example=true),
     *      @OA\Property(property="data", type="object"),
     *      @OA\Property(property="message", type="string", example="Task deleted successfully."),
     *    ),
     * ),
     * )
     */
    public function destroy(Task $tasks): JsonResponse
    {
        $tasks->delete();
    
        return $this->sendResponse([], 'Task deleted successfully.');
    }
}
