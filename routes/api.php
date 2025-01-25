<?php

use App\Http\Controllers\AuthController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/customer', [App\Http\Controllers\CustomerController::class, 'index']);

Route::middleware('auth:api')->get('/customers', [App\Http\Controllers\CustomerController::class, 'list']);
Route::middleware('auth:api')->post('/customer/create', [App\Http\Controllers\CustomerController::class, 'create']);
Route::middleware('auth:api')->put('/customer/update', [App\Http\Controllers\CustomerController::class, 'update']);
Route::middleware('auth:api')->get('/customer/read/{id}', [App\Http\Controllers\CustomerController::class, 'read']);
Route::middleware('auth:api')->delete('/customer/delete/{id}', [App\Http\Controllers\CustomerController::class, 'delete']);

Route::middleware(['auth:api'])->post('/oauth-test', [AuthController::class, 'oauthtest'])->name('oauthtest');


Route::post('register', [AuthController::class, 'register'])->name('register');

Route::post('login', [AuthController::class, 'store'])->name('login');

Route::post('send-code', [AuthController::class, 'sendEmailVerificationCode'])->name('sendcode');



