<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAdsApiController;

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

Route::get('/', function () {
    return view('main');
});
Route::match(
    ['get', 'post'],
    'getCode',
    [GoogleAdsApiController::class, 'getCodeAction']
);
Route::get(
    'create-campaign',
    [GoogleAdsApiController::class, 'createCampaignAction']
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
