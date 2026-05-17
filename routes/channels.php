<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.private.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.group.{id}', function ($user, $id) {
    return $user->groupMemberships()->where('group_id', $id)->exists() ? $user : false;
});

Broadcast::channel('chat.online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
