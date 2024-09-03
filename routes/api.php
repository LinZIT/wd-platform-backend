<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

/**---------------------
 * RUTAS SIN TOKEN
 * ---------------------**/
// Registrar master
Route::post('register/master/24548539', [AuthController::class, 'register_master']);
// Login
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    /**---------------------
     * USERS
     * ---------------------**/
    // Validacion de token
    Route::get('user/data', [AuthController::class, 'get_logged_user_data']);
    // Registrar usuario
    Route::post('user/add', [AuthController::class, 'register']);
    Route::get('get_users', [AuthController::class, 'get_all_users']);
    Route::post('send-message', [ChatMessageController::class, 'store']);
    Route::post('get-unread-messages', [ChatMessageController::class, 'getUnreadMessages']);

    // Websocket authorization 
    // Route::post('broadcasting/auth', [AuthController::class, 'index']);
});
