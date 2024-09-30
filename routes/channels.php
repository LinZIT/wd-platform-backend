<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('chat.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('status_online.{isOnline}', function (User $user, $isOnline) {
    return $user;
});
Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return $user;
});
Broadcast::channel('ticketsView.{roomId}', function (User $user, $roomId) {
    return (int) $user->department_id === (int) $roomId;
});
