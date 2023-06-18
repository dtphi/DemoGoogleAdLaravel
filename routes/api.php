<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Users\LoginApi as UserLogin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// sail composer dumpautoload
Route::post('/v1/user-login', [UserLogin::class, 'login'])->name('user.login');

Route::middleware('auth:api')->get('/v1/user', function (Request $request) {
    return response()->json([
        'status' => 1,
        'message' => 'successfull'
    ]);
});