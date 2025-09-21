<?php

namespace App\Filament\Pages\Auth;

use App\Models\Shops;
use App\Models\Subscriptions;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make()
                    ->steps([
                        Wizard\Step::make('Account Details')
                            ->description('Set up your email and password')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->revealable()
                                    ->maxLength(255),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->same('password')
                                    ->label('Confirm Password')
                                    ->revealable()
                                    ->required(),
                            ]),
                        Wizard\Step::make('Shop Details')
                            ->description('Enter your shop details')
                            ->schema([
                                TextInput::make('shop_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('shop_address')
                                    ->required()
                                    ->maxLength(255),
                                TextArea::make('shop_description')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                            wire:submit="register"
                        >
                            Register
                        </x-filament::button>
                        BLADE))),
            ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRegistration(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $plainPassword = $data['password'];

            if (!str_starts_with($plainPassword, '$2y$')) {
                $plainPassword = Hash::make($plainPassword);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $plainPassword,
                'role' => 'Admin',
            ]);
            Shops::create([
                'user_id' => $user->id,
                'shop_name' => $data['shop_name'],
                'shop_address' => $data['shop_address'],
                'shop_description' => $data['shop_description'],
            ]);

            Subscriptions::create([
                'user_id' => $user->id,
            ]);

            return $user;
        });
    }
}
