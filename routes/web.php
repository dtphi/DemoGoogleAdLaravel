<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAdsApiController;
use App\Http\Controllers\Api\Users\LoginApi as UserLogin;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

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

// Google ads
Route::get('/googleads', function () {
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
Route::post(
    'update-campaign',
    [GoogleAdsApiController::class, 'updateCampaignAction']
);
Route::match(
    ['get', 'post'],
    'delete-campaign',
    [GoogleAdsApiController::class, 'deleteCampaignAction']
);
Route::match(
    ['get', 'post'],
    'pause-campaign',
    [GoogleAdsApiController::class, 'pauseCampaignAction']
);
Route::match(
    ['get', 'post'],
    'show-report',
    [GoogleAdsApiController::class, 'showReportAction']
);


// Passport
Route::group([
    'as' => 'passport.',
    'prefix' => config('passport.path', 'oauth'),
    'namespace' => '\Laravel\Passport\Http\Controllers',
], function () {
    Route::post('/token', [
        'uses' => 'AccessTokenController@issueToken',
        'as' => 'token',
        'middleware' => 'throttle',
    ]);

    Route::get('/authorize', [
        'uses' => 'AuthorizationController@authorize',
        'as' => 'authorizations.authorize',
        'middleware' => 'web',
    ]);

    $guard = config('passport.guard', null);

    Route::middleware(['web', $guard ? 'auth:'.$guard : 'auth'])->group(function () {
        Route::post('/token/refresh', [
            'uses' => 'TransientTokenController@refresh',
            'as' => 'token.refresh',
        ]);

        Route::post('/authorize', [
            'uses' => 'ApproveAuthorizationController@approve',
            'as' => 'authorizations.approve',
        ]);

        Route::delete('/authorize', [
            'uses' => 'DenyAuthorizationController@deny',
            'as' => 'authorizations.deny',
        ]);

        Route::get('/tokens', [
            'uses' => 'AuthorizedAccessTokenController@forUser',
            'as' => 'tokens.index',
        ]);

        Route::delete('/tokens/{token_id}', [
            'uses' => 'AuthorizedAccessTokenController@destroy',
            'as' => 'tokens.destroy',
        ]);

        Route::get('/clients', [
            'uses' => 'ClientController@forUser',
            'as' => 'clients.index',
        ]);

        Route::post('/clients', [
            'uses' => 'ClientController@store',
            'as' => 'clients.store',
        ]);

        Route::put('/clients/{client_id}', [
            'uses' => 'ClientController@update',
            'as' => 'clients.update',
        ]);

        Route::delete('/clients/{client_id}', [
            'uses' => 'ClientController@destroy',
            'as' => 'clients.destroy',
        ]);

        Route::get('/scopes', [
            'uses' => 'ScopeController@all',
            'as' => 'scopes.index',
        ]);

        Route::get('/personal-access-tokens', [
            'uses' => 'PersonalAccessTokenController@forUser',
            'as' => 'personal.tokens.index',
        ]);

        Route::post('/personal-access-tokens', [
            'uses' => 'PersonalAccessTokenController@store',
            'as' => 'personal.tokens.store',
        ]);

        Route::delete('/personal-access-tokens/{token_id}', [
            'uses' => 'PersonalAccessTokenController@destroy',
            'as' => 'personal.tokens.destroy',
        ]);
    });
});

// React App UI
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
