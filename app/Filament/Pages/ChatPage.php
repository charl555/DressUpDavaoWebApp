<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ChatPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected string $view = 'filament.pages.chat-page';

    protected static ?string $navigationLabel = 'Chats';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }
}
