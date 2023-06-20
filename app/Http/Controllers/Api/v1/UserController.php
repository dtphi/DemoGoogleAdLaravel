<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\Users\UserService;
use App\Http\Requests\Users\UserRequest;

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

        return $repository->getListUserAll();
    }

    public function store(UserRequest $request)
    {
        $repository = $this->uSv->userRepository();
        try {
            $json['user'] = $repository->createUser($request->all());
        } catch (\Exception $e) {
            $json['errors']['ER_001'] = $e->getMessage();
        }

        return response()->JSON([
            'result' => $json
        ]);
    }
}
