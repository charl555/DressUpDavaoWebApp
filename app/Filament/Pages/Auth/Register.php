<?php

namespace App\Filament\Pages\Auth;

use App\Models\Shops;
use App\Models\Subscriptions;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make()
                    ->steps([
                        Step::make('Shop Owner Agreement')
                            ->description('Read and agree to the Shop Owner Terms')
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('shop_agreement_text')
                                    ->default('
SHOP OWNER AGREEMENT

By registering as a Shop Owner (“Vendor”) on DressUp Davao (“Platform”), you agree to the following:

1. Purpose of the Platform
The Platform enables Vendors to list gowns, suits, and rental products, manage bookings, interact with customers, and operate a digital shop.

2. Vendor Responsibilities
• Provide accurate product details, pricing, sizing, and availability  
• Upload only legally owned or authorized images and 3D models  
• Maintain truthful shop information  
• Fulfill rentals and bookings as agreed with customers  
• Ensure submitted verification documents are legitimate and current  

3. Prohibited Actions
Vendors may not:
• Post fake, duplicated, or misleading products  
• Misuse customer data  
• Circumvent or bypass platform systems  
• Upload harmful, stolen, or copyrighted 3D models  
• Harass customers or engage in illicit activities  

4. Verification
Vendor shop activation requires document verification (business permits, IDs, certifications). Failure to provide accurate documents may result in rejection or suspension.

5. Liability
The Vendor is solely responsible for all their listings, content, 3D models, customer interactions, and bookings.

6. Platform Rights
The Platform may suspend, remove, or deactivate shops that violate these terms.

By checking the agreement box, you confirm that you understand and accept these terms.
            ')
                                    ->disabled()
                                    ->rows(12)
                                    ->columnSpan('full')
                                    ->extraAttributes(['class' => 'bg-gray-100 text-gray-700 border border-gray-200 rounded']),
                                \Filament\Forms\Components\Checkbox::make('shop_agreement')
                                    ->label('I have read and agree to the Shop Owner Agreement.')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'You must agree to the Shop Owner Agreement before continuing.',
                                    ]),
                            ]),
                        Step::make('Account Details')
                            ->description('Set up your email and password')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter your full name')
                                    ->autofocus()
                                    ->validationMessages([
                                        'required' => 'Please enter your full name.',
                                        'max' => 'Name cannot exceed 255 characters.',
                                    ]),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'email')
                                    ->placeholder('your.email@example.com')
                                    ->validationMessages([
                                        'required' => 'Please enter your email address.',
                                        'email' => 'Please enter a valid email address.',
                                        'unique' => 'This email is already registered.',
                                        'max' => 'Email cannot exceed 255 characters.',
                                    ]),
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->revealable()
                                    ->maxLength(255)
                                    ->rule(Password::default()
                                        ->min(8)
                                        ->letters()
                                        ->mixedCase()
                                        ->numbers()
                                        ->symbols())
                                    ->placeholder('Create a strong password')
                                    ->validationMessages([
                                        'required' => 'Please create a password.',
                                        'min' => 'Password must be at least 8 characters.',
                                    ])
                                    ->helperText('Password must be at least 8 characters with uppercase, lowercase, number, and symbol.'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required()
                                    ->same('password')
                                    ->revealable()
                                    ->placeholder('Re-enter your password')
                                    ->validationMessages([
                                        'required' => 'Please confirm your password.',
                                        'same' => 'Passwords do not match.',
                                    ]),
                            ]),
                        Step::make('Shop Details')
                            ->description('Enter your shop information')
                            ->schema([
                                TextInput::make('shop_name')
                                    ->label('Shop Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter your shop name')
                                    ->validationMessages([
                                        'required' => 'Please enter your shop name.',
                                        'max' => 'Shop name cannot exceed 255 characters.',
                                    ])
                                    ->helperText('This will be the public name of your shop.'),
                                TextInput::make('shop_address')
                                    ->label('Shop Address')
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('123 Main Street, Barangay Poblacion, Davao City')
                                    ->validationMessages([
                                        'required' => 'Please enter your shop address.',
                                        'max' => 'Address cannot exceed 500 characters.',
                                    ])
                                    ->helperText('Enter the complete address.'),
                            ]),
                    ])
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                            <x-filament::button
                                type="submit"
                                size="lg"
                                wire:submit="register"
                                class="w-full justify-center"
                            >
                                Create Account
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
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'Admin',
            ]);

            // Create shop
            $shop = Shops::create([
                'user_id' => $user->id,
                'shop_name' => $data['shop_name'],
                'shop_address' => $data['shop_address'],
                'shop_status' => 'Not Verified',
                'allow_3d_model_access' => false,
            ]);

            // Create subscription
            Subscriptions::create([
                'user_id' => $user->id,
            ]);

            // Log shop owner registration
            \Log::info('Shop owner registered via Filament', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'shop_id' => $shop->shop_id,
                'shop_name' => $data['shop_name'],
                'ip_address' => request()->ip(),
                'registered_at' => now()->toDateTimeString(),
            ]);

            // Activity log for SuperAdmin
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'model_type' => 'Shop',
                'model_id' => $shop->shop_id,
                'description' => "Shop owner registered: {$user->name} ({$user->email}) - Shop: {$data['shop_name']}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $user;
        });
    }
}
