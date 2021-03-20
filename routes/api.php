<?php

use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\PictureController;
use App\Http\Controllers\Api\User\PictureController as UserPictureController;
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

// 图片
Route::name('api.pictures.')->prefix('pictures')->group(function ($route) {
    $route->get('', [PictureController::class, 'index'])->name('index');
    $route->get('/{picture:uuid}', [PictureController::class, 'show'])->name('show');
});

// 用户
Route::name('api.user.')->prefix('user')->group(function ($route) {
    $route->get('/{user:uuid}/pictures', [UserPictureController::class, 'index'])->name('pictures.index');
});

// 分享集
Route::name('api.collects.')->prefix('collects')->group(function ($route) {
    $route->get('', [CollectController::class, 'index'])->name('index');
    $route->get('/{collect:link}', [CollectController::class, 'show'])->name('show');
    $route->post('/{collect:link}/check_password', [CollectController::class, 'checkPassword'])->name('password.check');
});
// 需要登录
Route::name('api.collects.')->middleware('auth:sanctum')->prefix('collects')->group(function ($route) {
    // 创建分享集
    $route->post('', [CollectController::class, 'store'])->name('store');
    // 修改分享集
    $route->put('/{collect:link}', [CollectController::class, 'update']);
    // 给分享集点赞
    $route->post('/{collect:link}/like', [CollectController::class, 'like'])->name('like');
});
// 需要登录才可以操作的接口
Route::middleware('auth:sanctum')->group(function($route) {

    $route->get('me', [UserController::class, 'me']);
    // 上传图片
    $route->post('upload', [UploadController::class, 'store']);
    // 给图片点赞
    $route->post('picture/{picture}/like', [PictureController::class, 'like']);

    // 用户
    $route->post('user', [UserController::class, 'update']);
    $route->get('user/collect', [UserController::class, 'collect']);
    // 修改密码
    $route->post('password', [UpdatePasswordController::class, 'update']);
    // 注销登录
    $route->delete('logout', [AuthenticatedSessionController::class, 'destroy']);
});
Route::get('collect', [CollectController::class, 'index']);

Route::get('collect/{collect:link}', [CollectController::class, 'show']);
// 标签
Route::get('tags', [TagController::class, 'index']);
Route::post('pictures', [PictureController::class, 'store']);
Route::get('all', [PictureController::class, 'all']);


