<?php

use App\Http\Controllers\MovieController;
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

Route::group(['prefix' => 'movies'], function () {
    Route::get('/get-all', [MovieController::class, "getAll"]);
    Route::get('/get-most-viewed-movie', [MovieController::class, "getMostViewedMovie"]);
    Route::get('/get-most-viewed-genre', [MovieController::class, "getMostViewedGenre"]);
    Route::get('/stream/{id}', [MovieController::class, "stream"]);
    Route::post('/update', [MovieController::class, "update"]);
    Route::post('/add', [MovieController::class, "store"]);
});
