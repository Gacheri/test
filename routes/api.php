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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//create password
Route::get('/mpesa/password','MpesaController@lipaNaMpesaPassword');

//post response for access token
Route::post('/mpesa/token','MpesaController@newAccessToken');

//stk push
Route::post('/mpesa/stk','MpesaController@stkPush')->name('lipa');

// callback url
Route::post('/stk/callback','MpesaController@MpesaResponse');

// callback url data response
Route::post('/stk/callback/data','MpesaController@SaveData');
