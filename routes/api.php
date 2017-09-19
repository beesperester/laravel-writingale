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

Route::options('/{any}', 'PreflightController@options')->where('any', '(.*)');

// api version 1.0
Route::group(['prefix' => 'v1.0'], function () {
    // tree REST interface
    Route::resource('tree', 'v1_0\TreeController');

    /* // tree branch REST interface
    Route::resource('tree.branch', 'v1_0\TreeBranchController', [
        'only' => [
            'index',
            'store',
        ],
    ]); */

    // branch REST interface
    Route::resource('branch', 'v1_0\BranchController');
});
