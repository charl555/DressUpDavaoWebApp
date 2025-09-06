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
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('phone_number')
                            ->required()
                            ->maxLength(11)
                            ->minLength(11)
                            ->extraAttributes(['maxlength' => 11])
                            ->numeric(),
                        TextInput::make('address')->required(),
                    ]),
            ]);
    }
}
