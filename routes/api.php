<?php

use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\TaskController;
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

Route::post('/login', [UserController::class, 'login']);

Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::get('/check-email/{email}', [UserController::class, 'emailExist']);
Route::get('/check-token/{token}', [UserController::class, 'checkToken']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


Route::group(['middleware' => 'api'], function ($routes) {
    Route::post('/registration', [UserController::class, 'register']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/refresh-token', [UserController::class, 'refreshToken']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::put('/update-profile', [UserController::class, 'updateProfile']);
    Route::put('/update-password', [UserController::class, 'updatePassword']);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/task/{id}', [TaskController::class, 'show']);
    Route::put('/task/status-change/{id}', [TaskController::class, 'update']);
    Route::post('/task', [TaskController::class, 'store']);
    Route::delete('/task/{id}', [TaskController::class, 'destroy']);
});
