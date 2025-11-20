<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'message_thrade', 'reciver_id', 'sender_id', 'message', 'reply_id', 'school_id', 'read_status', 'created_at', 'created_at'
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'reciver_id');
    }

    /**
     * Get the message thread.
     */
    public function messageThread()
    {
        return $this->belongsTo(MessageThrade::class, 'message_thrade');
    }
}
