<?php

use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\PictureController;
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


Route::get('sanctum/csrf-cookie', [Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);

// 登录注册相关
Route::post('authorizations', [AuthenticatedSessionController::class, 'store']);
Route::post('register', [RegisterUserController::class, 'store']);


// 用户信息
Route::get('user/{user}', [UserController::class, 'show']);

// 需要登录才可以操作的接口
Route::middleware('auth:sanctum')->group(function($route) {

    $route->get('me', [UserController::class, 'me']);
    // 上传图片
    $route->post('upload', [UploadController::class, 'store']);
    // 给图片点赞
    $route->post('picture/{picture}/like', [PictureController::class, 'like']);

    // 分享集
    $route->group(['prefix' => 'collect'], function ($route) {
        // 创建分享集
        $route->post('', [CollectController::class, 'store']);
        // 修改分享集
        $route->put('/{collect:link}', [CollectController::class, 'update']);
        // 给分享集点赞
        $route->post('/{collect:link}/like', [CollectController::class, 'like']);
    });

    // 用户
    $route->post('user', [UserController::class, 'update']);
    $route->get('user/collect', [UserController::class, 'collect']);
    // 修改密码
    $route->post('password', [UpdatePasswordController::class, 'update']);
    // 注销登录
    $route->delete('logout', [AuthenticatedSessionController::class, 'destroy']);
});
Route::get('collect', [CollectController::class, 'index']);
Route::post('collect/{collect:link}/check_password', [CollectController::class, 'checkPassword']);
Route::get('collect/{collect:link}', [CollectController::class, 'show']);
// 标签
Route::get('tags', [TagController::class, 'index']);
Route::post('pictures', [PictureController::class, 'store']);
Route::get('pictures', [PictureController::class, 'show']);
Route::get('random', [PictureController::class, 'index']);
Route::get('all', [PictureController::class, 'all']);


