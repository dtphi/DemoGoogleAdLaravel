<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class LoginApi extends Controller
{
    /**
     * Get accessToken from user login to call api.
     * 
     * @param Request $request
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
