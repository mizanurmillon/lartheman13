<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    protected $hidden = [
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'sender_id' => 'integer',
            'receiver_id' => 'integer',
            'conversation_id' => 'integer',
            'reply_to_message_id' => 'integer',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_message_id');
    }

    public function parentMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    public function statuses()
    {
        return $this->hasMany(MessageStatus::class);
    }

    // public function statusHistories()
    // {
    //     return $this->hasMany(MessageStatusHistory::class);
    // }
}
