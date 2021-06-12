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
/** public unprotected routes will go in here **/
Route::group(['middleware' => ['json.request']], function () {
    Route::post('/login', 'Auth\LoginController@login')->name('login.api'); // done
    Route::get('/users', 'UsersController@index')->name('users.api'); // done
    Route::get('/projects', 'ProjectsController@index')->name('projects.api'); // done

});


/** public protected routes will go in here **/
Route::group(['middleware' => ['json.request','auth:api']], function (){
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout.api'); // done
    /** users routes will gore in here **/

    Route::get('/users/{id}', 'UsersController@show')->name('users.show.api'); // done
    Route::patch('/users/{id}', 'UsersController@update')->name('users.update.api'); // done
    /** projects routes will gore in here **/
    Route::post('/projects', 'ProjectsController@store')->name('projects.store.api'); // done
    Route::get('/projects/{id}', 'ProjectsController@show')->name('projects.show.api'); //
    Route::patch('/projects/{id}', 'ProjectsController@update')->name('projects.update.api'); // done
    Route::delete('/projects/{id}', 'ProjectsController@destroy')->name('projects.destroy.api'); //

    /** admin routes will go in here **/
    Route::group(['middleware' => ['IsUserAdmin']], function () {
        Route::post('/users', 'UsersController@store')->name('user.store.api'); // done
        Route::delete('/users/{id}', 'UsersController@destroy')->name('users.destroy.api'); // done
    });
});





