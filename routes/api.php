<?php

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

// 客戶端須在header加上: {Authorization:Bearer $token}
Route::middleware('auth:sanctum')->post('user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('logout', function (Request $request) {
    $request->user()->tokens()->delete();
});
