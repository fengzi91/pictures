<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\PictureController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 登录注册相关
Route::post('authorizations', [LoginController::class, 'login']);
Route::post('register', [RegisterUserController::class, 'store']);


// 需要登录
Route::middleware('auth:sanctum')->group(function($route) {
    $route->post('collect', [CollectController::class, 'store']);
});
Route::post('collect/{collect:link}/check_password', [CollectController::class, 'checkPassword']);
Route::get('collect/{collect:link}', [CollectController::class, 'show']);
Route::post('pictures', [PictureController::class, 'store']);
Route::get('pictures', [PictureController::class, 'show']);
Route::get('random', [PictureController::class, 'index']);
Route::get('all', [PictureController::class, 'all']);


