<?php

use App\Technical\Project\ProjectController;
use App\Technical\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/project')->group(function () {
        Route::post('/create', [ProjectController::class, 'createProject']);
        Route::get('/get-all', [ProjectController::class, 'getProjects']);
    });
    Route::prefix('/user')->group(function () {
        Route::get('/logout', [UserController::class, 'logout']);
    });
});

Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
