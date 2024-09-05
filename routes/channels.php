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
Broadcast::channel('public', function ($user) {
    return;
});
Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return $user;
});
