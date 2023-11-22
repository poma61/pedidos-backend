<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\PedidosController;
use App\Http\Controllers\Api\PersonalController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::prefix('auth')->middleware(['jwt'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::prefix('sucursal')->middleware(['jwt'])->group(function () {
    Route::post('all', [SucursalController::class, 'index']);
    Route::post('new', [SucursalController::class, 'store']);
    Route::put('refresh', [SucursalController::class, 'update']);
    Route::post('delete', [SucursalController::class, 'destroy']);
});

Route::prefix('personal')->middleware(['jwt'])->group(function () {
    Route::post('all', [PersonalController::class, 'index']);
    Route::post('new', [PersonalController::class, 'store']);
    Route::put('refresh', [PersonalController::class, 'update']);
    Route::post('delete', [PersonalController::class, 'destroy']);
    Route::post('by-ci-personal', [PersonalController::class, 'recordByCi']);
});

Route::prefix('usuario')->middleware(['jwt'])->group(function () {
    Route::post('all', [UserController::class, 'index']);
    Route::post('new', [UserController::class, 'store']);
    Route::put('refresh', [UserController::class, 'update']);
    Route::post('delete', [UserController::class, 'destroy']);
});



Route::prefix('cliente')->middleware(['jwt'])->group(function () {
    Route::post('all', [ClienteController::class, 'index']);
    Route::post('new', [ClienteController::class, 'store']);
    Route::put('refresh', [ClienteController::class, 'update']);
    Route::post('delete', [ClienteController::class, 'destroy']);
    Route::post('carga-masiva', [ClienteController::class, 'importRecords']);
});



Route::prefix('pedido')->middleware(['jwt'])->group(function () {
    Route::post('all', [PedidosController::class, 'index']);
    Route::post('new', [PedidosController::class, 'store']);
    Route::put('refresh', [PedidosController::class, 'update']);
    Route::post('delete', [PedidosController::class, 'destroy']);
    Route::post('carga-masiva', [PedidosController::class, 'importRecords']);
});
