<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Users\LoginApi as UserLogin;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GoogleAdsApiController;

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
Route::post('/v1/user-login', [UserLogin::class, 'login'])->name('login');
Route::middleware('auth:api')->get('/v1/user', function () {
    return response()->json([
        'status' => 1,
        'message' => 'successfull'
    ]);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('/v1/users', UserController::class);
});

/**
 * Google ads api
 */
Route::group(['middleware' => 'web'], function() {
    Route::get(
        '/v1/get-campaign/{customerId}',
        [GoogleAdsApiController::class, 'getCampaignAction']
    );
    Route::post(
        '/v1/create-campaign/{customerId}',
        [GoogleAdsApiController::class, 'createCampaignAction']
    );
    Route::post(
        '/v1/update-campaign/{customerId}/{campaignId}',
        [GoogleAdsApiController::class, 'updateCampaignAction']
    );
    Route::post(
        '/v1/pause-campaign/{customerId}/{campaignId}',
        [GoogleAdsApiController::class, 'pauseCampaignAction']
    );
    Route::post(
        '/v1/delete-campaign/{customerId}/{campaignId}',
        [GoogleAdsApiController::class, 'deleteCampaignAction']
    );
    Route::match(
        ['get', 'post'],
        'v1/show-report/{customerId}',
        [GoogleAdsApiController::class, 'showReportAction']
    );
});
