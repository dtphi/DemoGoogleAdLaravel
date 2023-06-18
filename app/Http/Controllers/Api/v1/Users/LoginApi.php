<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LoginApi extends Controller
{
    /**
     * Get accessToken from user login to call api.
     * 
     * @param Request $request
     */
    public function login(Request $request)
    {
        $email = $request->input('email');
        $pass = $request->input('password');

        $user = new User();
        $user->name = "Test user";
        $user->email = $email;
        $user->password = $pass;

        $token = $user->createToken('LaravelSampleAppTest')->accessToken;

        return response()->JSON([
            'userEmail' => $user->email,
            'userName' => $user->name,
            'accessToken' => $token
        ]);
    }
}
