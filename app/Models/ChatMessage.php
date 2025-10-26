<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'message_type',
        'image_path',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Check if message has an image
     */
    public function hasImage()
    {
        return $this->message_type === 'image' && !empty($this->image_path);
    }

    /**
     * Check if message is an inquiry
     */
    public function isInquiry()
    {
        return $this->message_type === 'inquiry';
    }

    /**
     * Get the full image URL
     */
    public function getImageUrl()
    {
        if ($this->hasImage()) {
            return asset('uploads/' . $this->image_path);
        }
        return null;
    }

    /**
     * Get conversation between two users
     */
    public static function getConversation($userId1, $userId2)
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get latest message between two users
     */
    public static function getLatestMessage($userId1, $userId2)
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->latest()->first();
    }

    /**
     * Get unread messages count for a user
     */
    public static function getUnreadCount($userId)
    {
        return self::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get users who have conversations with the given user
     */
    public static function getConversationPartners($userId)
    {
        $senderIds = self::where('receiver_id', $userId)
            ->distinct()
            ->pluck('sender_id');

        $receiverIds = self::where('sender_id', $userId)
            ->distinct()
            ->pluck('receiver_id');

        $partnerIds = $senderIds->merge($receiverIds)->unique();

        return User::whereIn('id', $partnerIds)->get();
    }
}
