<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoginApi extends Controller
{
    /**
     * Get accessToken from user login to call api.
     *
     * @param Request $request
     */

    /**
     * @OA\Post(
     * path="/api/v1/user-login",
     * operationId="UserLogin",
     * tags={"UserLogin"},
     * summary="User Login Api",
     * description="User Login here",
     * @OA\RequestBody(
     *  @OA\JsonContent(),
     *  @OA\MediaType(
     *      mediaType="multipart/form-data",
     *      @OA\Schema(
     *          schema="Result",
     *          title="Sample schema for using references",
     *          @OA\Property(
     *              property="email",
     *              type="string",
     *          ),
     *          @OA\Property(
     *              property="password",
     *              type="password",
     *          ),
     *          example={"email": "test.user@example.com", "password": "password"}
     *      ),
     *  ),
     * ),
     * @OA\Response(
     *  response=201,
     *  description="Response successfully",
     *  @OA\JsonContent()
     * ),
     * @OA\Response(
     *  response=200,
     *  description="Response successfully",
     *  @OA\JsonContent()
     * ),
     * )
     */
    public function login(Request $request)
    {
        $json['email'] = $request->input('email');
        $json['accessToken'] = '';

        try {
            Auth::attempt($request->all());
            $user = Auth::user();

            $json['accessToken'] = $user->createToken('LaravelSampleAppTest')->accessToken;
        } catch (\Exception $e) {
            $json['error'] = $e->getMessage();
        }

        return response()->JSON($json);
    }
}
