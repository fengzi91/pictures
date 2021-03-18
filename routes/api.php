<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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

Route::get('sanctum/csrf-cookie', [Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);

// 登录注册相关
Route::post('authorizations', [AuthenticatedSessionController::class, 'store']);
Route::post('register', [RegisterUserController::class, 'store']);


// 需要登录才可以操作的接口
Route::middleware('auth:sanctum')->group(function($route) {
    // 上传图片
    $route->post('upload', [UploadController::class, 'store']);
    // 创建分享集
    $route->post('collect', [CollectController::class, 'store']);
    // 修改分享集
    $route->put('collect/{collect:link}', [CollectController::class, 'update']);
    // 用户
    $route->post('user', [UserController::class, 'update']);
    $route->get('user/collect', [UserController::class, 'collect']);
});
Route::post('collect/{collect:link}/check_password', [CollectController::class, 'checkPassword']);
Route::get('collect/{collect:link}', [CollectController::class, 'show']);
// 标签
Route::get('tags', [TagController::class, 'index']);
Route::post('pictures', [PictureController::class, 'store']);
Route::get('pictures', [PictureController::class, 'show']);
Route::get('random', [PictureController::class, 'index']);
Route::get('all', [PictureController::class, 'all']);


