<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\TicketController;
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
    Route::put('user/{user}/change/color', [AuthController::class, 'change_color']);
    Route::put('user/{user}/change/theme', [AuthController::class, 'change_theme']);
    Route::put('user/{user}/change/password', [AuthController::class, 'change_password']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('user/add', [AuthController::class, 'register']);
    Route::get('users/online', [AuthController::class, 'get_all_online_users']);
    Route::get('users/offline', [AuthController::class, 'get_all_offline_users']);

    // Tickets
    Route::get('tickets', [TicketController::class, 'index']);
    Route::get('ticket/{ticket}', [TicketController::class, 'get_ticket_by_id']);
    Route::post('ticket', [TicketController::class, 'store']);
    // Route::put('ticket/{ticket}/status', [TicketController::class, 'update_ticket_status']);
    Route::post('ticket/{ticket}/actualization', [TicketController::class, 'new_ticket_actualization']);
    Route::post('ticket-move/{ticket}', [TicketController::class, 'ticket_move']);

    // Ticket Categories
    Route::post('ticket/category', [TicketCategoryController::class, 'store']);

    // Department
    Route::post('department', [DepartmentController::class, 'create']);

    // Status
    Route::post('status', [StatusController::class, 'create']);
    Route::put('ticket/{ticket}/status', [TicketController::class, 'change_ticket_status']);

    // Ticket Priority
    Route::put('ticket/{ticket}/priority', [TicketController::class, 'change_ticket_priority']);

    // Roles
    Route::post('role', [RoleController::class, 'create']);

    // Chat Messages
    Route::post('send-message', [ChatMessageController::class, 'store']);
    Route::post('get-unread-messages', [ChatMessageController::class, 'getUnreadMessages']);
    Route::get('get/chat/{from}', [ChatMessageController::class, 'getChatMessages']);

    // Websocket authorization 
    // Route::post('broadcasting/auth', [AuthController::class, 'index']);
});
