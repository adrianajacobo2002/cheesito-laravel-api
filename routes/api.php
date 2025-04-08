<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatilloController;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'Hola desde la API de Cheesito',
        'status' => 'OK',
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'rol.admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Bienvenido, admin']);
    });

    Route::prefix('platillos')->group(function () {
        Route::get('/', [PlatilloController::class, 'index']);
        Route::get('/{id}', [PlatilloController::class, 'show']);
        Route::post('/', [PlatilloController::class, 'store']);
        Route::put('/{id}', [PlatilloController::class, 'update']); 
        Route::delete('/{id}', [PlatilloController::class, 'destroy']);
    });

    Route::get('/inventario', [InventarioController::class, 'index']);
    Route::put('/inventario/{id}/agregar', [InventarioController::class, 'agregarStock']);
    Route::get('/inventario/agotados', [InventarioController::class, 'agotados']);

    Route::get('/mesas', [MesaController::class, 'index']);
    Route::post('/mesas', [MesaController::class, 'store']);
    Route::put('/mesas/{id}', [MesaController::class, 'update']);
    Route::delete('/mesas/{id}', [MesaController::class, 'destroy']);
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

