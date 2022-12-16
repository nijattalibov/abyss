<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;

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

Route::fallback(function(){
    return response()->json(['success'=>FALSE,'message'=>'Not found'],200);
});

Route::group(['prefix'=>'persons'], function (){
    Route::get('/',[PersonController::class, 'index'])->name('person.index');
    Route::get('/{id}',[PersonController::class, 'detail'])->name('person.detail');
    Route::post('/',[PersonController::class, 'create'])->name('person.create');
    Route::post('/{id}',[PersonController::class, 'update'])->name('person.update');
    Route::delete('/{id}',[PersonController::class, 'delete'])->name('person.delete');
    Route::get('/get_image/{id}',[PersonController::class, 'get_image'])->name('person.get_image');
});

