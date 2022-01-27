<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TelegramApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::match(['get','post'], 'auth', [TelegramApiController::class, 'auth']);
Route::match(['get','post'], 'logout', [TelegramApiController::class, 'logout']);
Route::match(['get','post'], 'get-message/{channel}', [TelegramApiController::class, 'getMessage']);

Route::resource('task', TaskController::class);

Route::group(
    [
        'prefix' => 'group',
        'as' => 'group',
        'middleware' => [],
    ],
    function () {
        Route::post('download-message/{name}', [GroupController::class, 'downloadMessage']);
    }
);
