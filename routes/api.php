<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['auth:api', 'throttle:10,5']], function(){
	Route::get('articles', 'API\ArticleController@index');
	Route::get('article/{id}', 'API\ArticleController@show');
	Route::post('article', 'API\ArticleController@store');
	Route::put('article', 'API\ArticleController@store');
	Route::delete('article/{id}', 'API\ArticleController@destroy');
});