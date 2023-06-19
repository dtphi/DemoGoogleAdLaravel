<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAdsApiController;
use App\Http\Controllers\Api\Users\LoginApi as UserLogin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('test-get-accesstoken-api', [UserLogin::class, 'login']);
Route::get('/', function () {
    return view('main');
});
Route::match(
    ['get', 'post'],
    'getCode',
    [GoogleAdsApiController::class, 'getCodeAction']
);
Route::post(
    'create-campaign',
    [GoogleAdsApiController::class, 'createCampaignAction']
);
Route::get(
    'get-campaign',
    [GoogleAdsApiController::class, 'getCampaignAction']
);
Route::match(
    ['get', 'post'],
    'delete-campaign',
    [GoogleAdsApiController::class, 'deleteCampaignAction']
);
Route::post(
    'pause-campaign',
    [GoogleAdsApiController::class, 'pauseCampaignAction']
);
Route::match(
    ['get', 'post'],
    'show-report',
    [GoogleAdsApiController::class, 'showReportAction']
);
