<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SavedJobsController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $users = User::all()->load('roles');
    return response()->json($users);
});
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
        Route::get('/my-profile', [AuthController::class, 'myProfile']);
        Route::post('/my-profile', [AuthController::class, 'updateMyProfile']);
        Route::get("skills/job-post", [SkillController::class, 'jobPost'])->middleware('role:super_admin|admin|recruiter');
        Route::get("job-categories/job-post", [JobCategoryController::class, 'jobPost'])->middleware('role:super_admin|admin|recruiter');
        Route::apiResource('users', UserController::class)->middleware('role:super_admin|admin');
        Route::apiResource('companies', CompanyController::class)->middleware('role:super_admin|admin|recruiter');
        Route::apiResource('skills', SkillController::class)->middleware('role:super_admin|admin|recruiter');
        Route::apiResource('job-categories', JobCategoryController::class)->middleware('role:super_admin|admin|recruiter');
        Route::apiResource('job-posts', JobPostingController::class)->middleware('role:super_admin|admin|recruiter');
        Route::get('recruiters', [UserController::class, 'recruiter'])->middleware('role:super_admin|admin|recruiter');
    });
    Route::prefix('user')->group(function () {
        Route::get('/categories', [JobCategoryController::class, 'index']);
        Route::get('/featured-jobs', [JobPostingController::class, 'featuredJobs']);
        Route::get('/search-jobs', [JobPostingController::class, 'searchJobs']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/job-postings/application/{id}', [JobPostingController::class, 'apply']);
            Route::get('/job-postings/{slug}', [JobPostingController::class, 'searchJobsDetail']);
            Route::get('/reviews', [ReviewController::class, 'index']);
            Route::post('/reviews', [ReviewController::class, 'store']);
            Route::get("/save-jobs", [SavedJobsController::class, 'savedJobs']);
            Route::post("/save-jobs", [SavedJobsController::class, 'store']);
            Route::delete("/save-jobs/{id}", [SavedJobsController::class, 'destroy']);
            Route::post('/job-applications', [JobApplicationController::class, 'store']);
        });
    });
});
