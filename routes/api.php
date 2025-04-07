<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatilloController;


Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'rol.admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Bienvenido, admin']);
    });
    Route::post('/platillos', [PlatilloController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'rol.mesero'])->group(function () {
    Route::get('/mesero/ordenes', function () {
        return response()->json(['message' => 'Bienvenido, mesero']);
    });
});

Route::middleware(['auth:sanctum', 'rol.cocinero'])->group(function () {
    Route::get('/cocinero/tareas', function () {
        return response()->json(['message' => 'Bienvenido, cocinero']);
    });
});

