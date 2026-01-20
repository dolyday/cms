<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\CategoryController;
use App\Http\Controllers\Auth\CommentController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\Auth\DraftController;
use App\Http\Controllers\Auth\PostController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\TagController;
use App\Http\Controllers\Auth\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/**
 *  User routes ( Guest )
 */
Route::middleware('guest:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        // Login
        Route::post('/login', [AuthController::class, 'login']);

        // Register
        Route::post('/register', [AuthController::class, 'register']);
    });
});


/**
 *  User routes ( Authentication )
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            // Dashboard
            Route::get('/dashboard', 'index');

            // Logout
            Route::post('/logout', 'logout');
        });

        // Categories + Tags + Posts + Roles + Users
        Route::apiResources([
            'categories' => CategoryController::class,
            'tags'       => TagController::class,
            'posts'      => PostController::class,
            'roles'      => RoleController::class,
            'users'      => UserController::class
        ]);

        // Comments
        Route::controller(CommentController::class)->group(function () {
            Route::get('/comments', 'getComments');
            Route::put('/comments/change-status/{id}', 'changeStatus');
        });

        // Drafts
        Route::controller(DraftController::class)->group(function () {
            Route::post('/drafts', 'store');
            Route::delete('/drafts/{id}', 'destroy');
        });

        // Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'details');
            Route::put('/profile', 'update');
            Route::put('/profile/change-password', 'changePassword');
        });
    });
});


// Home
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index');
});

// Blog
Route::controller(BlogController::class)->prefix('blog')->group(function () {
    Route::get('/', 'index');
    Route::get('/{slug}', 'details');
    Route::get('/tags/{slug}', 'tag');
    Route::get('/authors/{name}', 'author');
    Route::get('/categories/{slug}', 'category');
    Route::post('/save-comment/{postID}/{parentID?}/{replyID?}', 'saveComment');
});