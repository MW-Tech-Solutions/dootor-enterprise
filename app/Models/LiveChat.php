<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'agent_id', 'status', 'queue_position', 'subject', 'accepted_at', 'closed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages()
    {
        return $this->hasMany(LiveChatMessage::class, 'chat_id')->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(LiveChatMessage::class, 'chat_id')->latestOfMany();
    }
}
