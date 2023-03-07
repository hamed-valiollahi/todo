<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     * path="/register",
     * summary="Register",
     * description="Register by name, email, password",
     * operationId="register",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Register",
     *    @OA\JsonContent(
     *       required={"name","email","password","c_password"},
     *       @OA\Property(property="name", type="string", example="Ryan"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="c_password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Validation error response",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Validation Error."),
     *       @OA\Property(property="data", type="array",
     *          @OA\Items(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="The name field is required."
     *              ),
     *          ),
     *      ),
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="User register successfully.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="token", type="string", example="8|827y6FdrBwYLbQNu7JMDNMmzsz54UlQlEnbjtCxp"),
     *          @OA\Property(property="name", type="string", example="Ryan"),
     *      ),
     *      @OA\Property(property="message", type="string", example="User register successfully."),
     *    ),
     * )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 400);       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     * path="/login",
     * summary="Login",
     * description="Login by email, password",
     * operationId="login",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again!"),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="error", type="string", example="Unauthorised"),
     *      ),
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="User login successfully.",
     *    @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="data", type="object",
     *          @OA\Property(property="token", type="string", example="6|B7irTXD3f5x7dYMffDvbNfs9hdqDZJbJhl2ESEeQ"),
     *          @OA\Property(property="name", type="string", example="Hamed"),
     *      ),
     *       @OA\Property(property="message", type="string", example="User login successfully."),
     *    )
     * )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Sorry, wrong email address or password. Please try again!', ['error'=>'Unauthorised'], 401);
        } 
    }
}
