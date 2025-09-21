<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get conversation between current user and another user
     */
    public function getConversation(Request $request, $userId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentUserId = Auth::id();
        $messages = ChatMessage::getConversation($currentUserId, $userId);

        // Mark messages as read if current user is the receiver
        ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'messages' => $messages->load(['sender', 'receiver']),
            'current_user_id' => $currentUserId
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        $message->load(['sender', 'receiver']);

        // Broadcast the message using Pusher
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => $message,
            'status' => 'Message sent successfully'
        ]);
    }

    /**
     * Get conversation partners (users who have chatted with current user)
     */
    public function getConversationPartners()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentUserId = Auth::id();
        $partners = ChatMessage::getConversationPartners($currentUserId);

        // Add latest message and unread count for each partner
        $partnersWithDetails = $partners->map(function ($partner) use ($currentUserId) {
            $latestMessage = ChatMessage::getLatestMessage($currentUserId, $partner->id);
            $unreadCount = ChatMessage::where('sender_id', $partner->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $partner->id,
                'name' => $partner->name,
                'email' => $partner->email,
                'role' => $partner->role,
                'latest_message' => $latestMessage ? $latestMessage->message : null,
                'latest_message_time' => $latestMessage ? $latestMessage->created_at : null,
                'unread_count' => $unreadCount,
            ];
        });

        return response()->json($partnersWithDetails);
    }

    /**
     * Get all admins (for users to start conversations with)
     */
    public function getAdmins()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admins = User::whereIn('role', ['Admin', 'SuperAdmin'])->get(['id', 'name', 'email', 'role']);

        return response()->json($admins);
    }

    /**
     * Get all users (for admins to see all potential conversations)
     */
    public function getAllUsers()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $users = User::where('id', '!=', Auth::id())
            ->get(['id', 'name', 'email', 'role']);

        return response()->json($users);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = ChatMessage::getUnreadCount(Auth::id());

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Send an inquiry message to the product owner
     */
    public function sendInquiry(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'message' => 'required|string|max:1000',
            'rental_date' => 'required|date|after:today',
            'original_message' => 'required|string|max:500'
        ]);

        try {
            // Get the product and its owner
            $product = Products::with('user')->findOrFail($request->product_id);
            $productOwner = $product->user;
            $currentUser = Auth::user();

            // Don't allow users to send inquiries to themselves
            if ($currentUser->id === $productOwner->id) {
                return response()->json(['error' => 'You cannot inquire about your own product'], 400);
            }

            // Create the chat message
            $message = ChatMessage::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $productOwner->id,
                'message' => $request->message,
                'is_read' => false
            ]);

            // Broadcast the message if Pusher is configured
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::warning('Failed to broadcast inquiry message: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'receiver']),
                'product' => [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'owner' => $productOwner->name
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending inquiry: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send inquiry'], 500);
        }
    }
}
