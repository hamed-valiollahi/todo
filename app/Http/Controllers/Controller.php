<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     description="Simple Todo Restful API",
 *     version="1.0.0",
 *     title="Schema App - Todo",
 *     termsOfService="https://www.domain.com",
 *     @OA\Contact(
 *         email="hamedv90@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Everything about your Authentication",
 *     @OA\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://localhost:8000/api"
 *     )
 * )
 * @OA\Tag(
 *     name="Category",
 *     description="Everything about your Categories",
 *     @OA\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://localhost:8000/api"
 *     )
 * )
 * @OA\Tag(
 *     name="Task",
 *     description="Everything about your Tasks",
 *     @OA\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://localhost:8000/api"
 *     )
 * )
 * @OA\Server(
 *     description="Todo",
 *     url="http://localhost:8000/api"
 * )
 * @OA\ExternalDocumentation(
 *     description="Find out more about Todo",
 *     url="https://www.domain.com"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
