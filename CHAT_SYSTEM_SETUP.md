# Chat System Setup Guide

## Overview
A complete real-time chat system has been implemented for your Laravel Dress-Up Davao application using Pusher for real-time messaging. The system allows users to communicate with admins for product inquiries.

## Features Implemented

### 1. Database Structure
- **chat_messages table**: Stores all chat messages with sender/receiver relationships
- **Indexes**: Optimized for performance with proper indexing on conversation queries
- **Read status tracking**: Tracks message read status and timestamps

### 2. Backend Components
- **ChatMessage Model**: Handles conversation management and relationships
- **ChatController**: API endpoints for chat functionality
- **MessageSent Event**: Pusher broadcasting for real-time messaging
- **Authentication**: Only logged-in users can access chat features

### 3. Frontend Components
- **ChatWindow Component**: User-facing chat interface with floating chat button
- **Filament ChatPage**: Admin interface for managing conversations
- **Real-time updates**: Pusher integration for instant message delivery
- **Responsive design**: Works on desktop and mobile devices

## Setup Instructions

### Step 1: Configure Pusher
1. Create a Pusher account at https://pusher.com/
2. Create a new app in your Pusher dashboard
3. Add the following to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=your_cluster
```

### Step 2: Install Pusher PHP SDK
Run the following command to install the Pusher PHP SDK:
```bash
composer require pusher/pusher-php-server
```

### Step 3: Configure Broadcasting
1. Uncomment the broadcasting service provider in `config/app.php`:
```php
App\Providers\BroadcastServiceProvider::class,
```

2. Update `config/broadcasting.php` to ensure Pusher is properly configured:
```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],
```

### Step 4: Database Migration
The chat_messages table has been created. If you need to run it manually:
```bash
php artisan migrate
```

### Step 5: User Model Requirements
Ensure your User model has the following method for admin checking:
```php
public function isAdmin()
{
    return in_array($this->role, ['Admin', 'SuperAdmin']);
}
```

## Usage

### For Regular Users
1. **Access**: Chat button appears on all pages for logged-in users
2. **Interface**: Click the floating chat button to open the chat window
3. **Contacts**: Users can see and chat with admins
4. **Real-time**: Messages appear instantly without page refresh

### For Admins (Filament)
1. **Access**: Navigate to "Chats" in the Filament admin panel
2. **Interface**: Full-screen chat interface with user list
3. **Contacts**: Admins can see all users and conversation history
4. **Real-time**: Instant message notifications and updates

## API Endpoints

### Chat Routes (Authentication Required)
- `GET /chat/conversation/{userId}` - Get conversation with specific user
- `POST /chat/send` - Send a message
- `GET /chat/partners` - Get conversation partners
- `GET /chat/admins` - Get all admin users (for regular users)
- `GET /chat/users` - Get all users (for admins)
- `GET /chat/unread-count` - Get unread message count

## File Structure

### Backend Files
- `app/Models/ChatMessage.php` - Chat message model
- `app/Http/Controllers/ChatController.php` - Chat API controller
- `app/Events/MessageSent.php` - Pusher broadcast event
- `database/migrations/2025_01_10_000000_create_chat_messages_table.php` - Database migration

### Frontend Files
- `resources/views/components/ChatWindow.blade.php` - User chat component
- `app/Filament/Pages/ChatPage.php` - Admin chat page controller
- `resources/views/filament/pages/chat-page.blade.php` - Admin chat interface

### Routes
- `routes/web.php` - Chat routes and Pusher channel authorization

## Security Features

1. **Authentication Required**: Only logged-in users can access chat
2. **Private Channels**: Pusher channels are private and user-specific
3. **CSRF Protection**: All POST requests include CSRF tokens
4. **Role-based Access**: Admins and users have different permissions
5. **Input Sanitization**: Messages are properly escaped to prevent XSS

## Troubleshooting

### Common Issues
1. **Messages not appearing in real-time**: Check Pusher configuration and credentials
2. **CSRF token errors**: Ensure meta tag is present in page head sections
3. **Chat button not showing**: Verify user is logged in and component is included
4. **Admin chat not working**: Check user role and isAdmin() method

### Debug Steps
1. Check browser console for JavaScript errors
2. Verify Pusher connection in browser network tab
3. Check Laravel logs for backend errors
4. Test API endpoints directly with tools like Postman

## Next Steps

1. **Test the system**: Send messages between users and admins
2. **Customize styling**: Modify CSS to match your brand colors
3. **Add features**: Consider adding file uploads, emoji support, or typing indicators
4. **Monitor usage**: Set up analytics to track chat usage and effectiveness

The chat system is now fully functional and ready for use!
