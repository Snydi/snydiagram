<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(["prefix" => "diagrams", "as" => "dashboard."], function () {
        Route::get('/', [DiagramController::class, 'index']);
        Route::get('/{id}', [DiagramController::class, 'show']);
        Route::post('/', [DiagramController::class, 'store']);
        Route::put('/{id}', [DiagramController::class, 'update']);
        Route::delete('/{id}', [DiagramController::class, 'destroy']);
    });
});
