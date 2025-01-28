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
Broadcast::channel('department_room.{department_id}', function (User $user, $department_id) {
    if ((int) $user->department_id === (int) $department_id) return $user;
    return false;
});
Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return $user;
});
Broadcast::channel('ticketsRoom.{department_id}', function (User $user, $department_id) {
    if ((int) $user->department_id === (int) $department_id) return $user;
});
