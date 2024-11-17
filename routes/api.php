<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user()->load('roles');
        });
        Route::apiResource('users', UserController::class)->middleware('role:super_admin|admin');
        Route::apiResource('companies', CompanyController::class)->middleware('role:super_admin|admin|recruiter');
        Route::apiResource('skills', SkillController::class)->middleware('role:super_admin|admin');
        Route::apiResource('job-categories', JobCategoryController::class)->middleware('role:super_admin|admin');
        Route::apiResource('job-posts', JobPostingController::class)->middleware('role:super_admin|admin|recruiter');
        Route::get('recruiter', [UserController::class, 'recruiter'])->middleware('role:super_admin|admin');
    });
});
