<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Bookings;
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
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',  // 5MB max
            'message_type' => 'nullable|in:text,image'
        ]);

        $messageData = [
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '',
            'message_type' => $request->message_type ?? 'text',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('chat-images', 'public');
            $messageData['image_path'] = $imagePath;
            $messageData['message_type'] = 'image';
        }

        // Validate that either message or image is provided
        if (empty($messageData['message']) && empty($messageData['image_path'])) {
            return response()->json(['error' => 'Either message or image is required'], 400);
        }

        $message = ChatMessage::create($messageData);
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
     * Get admins that the current user has conversations with (for regular users)
     */
    public function getAdmins()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentUserId = Auth::id();

        // Only return admins that the user has had conversations with
        $adminIds = ChatMessage::where(function ($query) use ($currentUserId) {
            $query
                ->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId);
        })
            ->where(function ($query) use ($currentUserId) {
                $query
                    ->where('sender_id', '!=', $currentUserId)
                    ->orWhere('receiver_id', '!=', $currentUserId);
            })
            ->get()
            ->map(function ($message) use ($currentUserId) {
                return $message->sender_id === $currentUserId ? $message->receiver_id : $message->sender_id;
            })
            ->unique();

        $admins = User::whereIn('id', $adminIds)
            ->whereIn('role', ['Admin', 'SuperAdmin'])
            ->get(['id', 'name', 'email', 'role']);

        return response()->json($admins);
    }

    /**
     * Get users that have initiated conversations with the current admin
     */
    public function getAllUsers()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentUserId = Auth::id();

        // Only return users that have had conversations with this admin
        $userIds = ChatMessage::where(function ($query) use ($currentUserId) {
            $query
                ->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId);
        })
            ->where(function ($query) use ($currentUserId) {
                $query
                    ->where('sender_id', '!=', $currentUserId)
                    ->orWhere('receiver_id', '!=', $currentUserId);
            })
            ->get()
            ->map(function ($message) use ($currentUserId) {
                return $message->sender_id === $currentUserId ? $message->receiver_id : $message->sender_id;
            })
            ->unique();

        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', $currentUserId)
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
            'original_message' => 'required|string|max:500',
            'thumbnail_path' => 'nullable|string'
        ]);

        try {
            // Get the product and its owner with images
            $product = Products::with(['user', 'product_images'])->findOrFail($request->product_id);
            $productOwner = $product->user;
            $currentUser = Auth::user();

            // Don't allow users to send inquiries to themselves
            if ($currentUser->id === $productOwner->id) {
                return response()->json(['error' => 'You cannot inquire about your own product'], 400);
            }

            // Get product thumbnail image
            $thumbnailPath = null;

            // Use provided thumbnail path or get from product images
            if ($request->thumbnail_path) {
                $originalPath = storage_path('app/public/' . $request->thumbnail_path);
                if (file_exists($originalPath)) {
                    $fileName = 'inquiry_' . time() . '_' . basename($request->thumbnail_path);
                    $newPath = 'chat-images/' . $fileName;
                    $fullNewPath = storage_path('app/public/' . $newPath);

                    // Create directory if it doesn't exist
                    $directory = dirname($fullNewPath);
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }

                    // Copy the file
                    if (copy($originalPath, $fullNewPath)) {
                        $thumbnailPath = $newPath;
                    }
                }
            } else {
                // Fallback to product images
                $productImage = $product->product_images()->first();
                if ($productImage && $productImage->thumbnail_image) {
                    $originalPath = storage_path('app/public/' . $productImage->thumbnail_image);
                    if (file_exists($originalPath)) {
                        $fileName = 'inquiry_' . time() . '_' . basename($productImage->thumbnail_image);
                        $newPath = 'chat-images/' . $fileName;
                        $fullNewPath = storage_path('app/public/' . $newPath);

                        // Create directory if it doesn't exist
                        $directory = dirname($fullNewPath);
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Copy the file
                        if (copy($originalPath, $fullNewPath)) {
                            $thumbnailPath = $newPath;
                        }
                    }
                }
            }

            // Create the chat message with product metadata
            $messageData = [
                'sender_id' => $currentUser->id,
                'receiver_id' => $productOwner->id,
                'message' => $request->message,
                'message_type' => 'inquiry',
                'is_read' => false,
                'metadata' => [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'rental_date' => $request->rental_date,
                    'original_message' => $request->original_message
                ]
            ];

            // Add image if available
            if ($thumbnailPath) {
                $messageData['image_path'] = $thumbnailPath;
            }

            $message = ChatMessage::create($messageData);

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

    /**
     * Get available products for booking (admin only)
     * Returns all rentable products (not in maintenance status)
     * Date-specific availability is checked when creating the booking
     */
    public function getAvailableProducts()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all rentable products (not in maintenance status)
        // Products can be booked for future dates even if currently rented
        $products = Products::where('user_id', Auth::id())
            ->whereNotIn('status', Products::MAINTENANCE_STATUSES)
            ->where('visibility', 'Yes')
            ->get(['product_id', 'name', 'type', 'rental_price']);

        return response()->json($products);
    }

    /**
     * Create a booking reservation (admin only)
     */
    public function createBooking(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,product_id',
            'booking_date' => 'required|date|after:today',
        ]);

        try {
            // Check if product exists and belongs to the admin
            $product = Products::findOrFail($request->product_id);

            if ($product->user_id !== Auth::id()) {
                return response()->json(['error' => 'You can only book your own products'], 403);
            }

            // Check if product is rentable (not in maintenance status)
            if (!$product->isRentable()) {
                return response()->json(['error' => 'Product is currently under maintenance and not available for booking'], 400);
            }

            // Check if the date is available (no conflicts with existing rentals or bookings)
            if (!$product->isDateAvailable($request->booking_date)) {
                return response()->json(['error' => 'Product is not available for the selected date. Please check the availability calendar.'], 400);
            }

            // Create the booking
            $booking = Bookings::create([
                'user_id' => $request->user_id,
                'created_by' => Auth::id(),
                'product_id' => $request->product_id,
                'booking_date' => $request->booking_date,
                'status' => 'On Going',
            ]);

            // Note: Product status is now determined dynamically based on rental/booking dates
            // No need to manually update product status

            // Send a notification message to the user
            $user = User::findOrFail($request->user_id);
            $notificationMessage = ChatMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->user_id,
                'message' => "🎉 Booking Confirmed!\n\nProduct: {$product->name}\nReservation Date: {$request->booking_date}\n\nPlease visit our shop on the scheduled date to view and potentially rent this item. Thank you!",
                'message_type' => 'text',
                'metadata' => [
                    'booking_id' => $booking->booking_id,
                    'type' => 'booking_confirmation'
                ]
            ]);

            // Broadcast the notification
            broadcast(new MessageSent($notificationMessage))->toOthers();

            return response()->json([
                'success' => true,
                'booking' => $booking->load(['user', 'product']),
                'message' => 'Booking created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating booking: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create booking'], 500);
        }
    }
}
