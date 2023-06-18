<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\Users\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $uSv
    ){}

    /**
     * Get users api.
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        $repository = $this->uSv->userRepository();

        return response()->JSON($repository->testData());
    }
}
