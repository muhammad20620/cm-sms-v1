<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\MessageThrade;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// User private channel for receiving messages
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Message thread channel - only participants can listen
Broadcast::channel('message-thread.{threadId}', function ($user, $threadId) {
    $thread = MessageThrade::find($threadId);
    
    if (!$thread) {
        return false;
    }
    
    // Check if user is either sender or receiver in this thread
    return (int) $user->id === (int) $thread->sender_id || 
           (int) $user->id === (int) $thread->reciver_id;
});
