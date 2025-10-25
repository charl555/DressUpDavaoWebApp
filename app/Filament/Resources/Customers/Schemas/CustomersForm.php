<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Customer Information')
                    ->description("Enter the customer's personal details for rental records")
                    ->icon('heroicon-o-user')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->helperText("Enter the customer's first name.")
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g., Maria')
                            ->rules(['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s]+$/']),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->helperText("Enter the customer's last name or surname")
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g., Santos')
                            ->rules(['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s]+$/']),
                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->helperText('Enter 11-digit Philippine mobile number (e.g., 09123456789)')
                            ->required()
                            ->maxLength(11)
                            ->minLength(11)
                            ->extraAttributes(['maxlength' => 11])
                            ->numeric()
                            ->placeholder('09123456789')
                            ->rules(['required', 'numeric', 'digits:11', 'regex:/^09[0-9]{9}$/']),
                        TextInput::make('address')
                            ->label('Address')
                            ->helperText("Enter the customer's address")
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 123 Main Street, Barangay Poblacion, Davao City')
                            ->rules(['required', 'string', 'max:255']),
                    ]),
            ]);
    }
}
