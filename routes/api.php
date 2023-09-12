<?php

use App\Http\Controllers\Api\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Api'], function () {
    // this writing doesnot work if you define gloabl name space in RouteServiceProvider.php

    // Route::post('/login', [UserController::class, 'createUser']); 
    Route::post('/login', 'UserController@createUser');
    // authhuntication middleware
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::any('/courseList', 'CourseController@courseList');
        Route::any('/courseDetail', 'CourseController@courseDetail');
        Route::any('/checkout', 'PayController@checkout');
    });
});