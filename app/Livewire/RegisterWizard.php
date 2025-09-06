<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterWizard extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function form(Wizard $form): Wizard
    {
        return $form
            ->schema([
                Wizard\Step::make('Account Details')
                    ->description('Set up your email and password')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->confirmed()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(),
                    ]),
                Wizard\Step::make('Style Preferences')
                    ->description('Choose your preferred styles')
                    ->schema([
                        Select::make('color_preference')
                            ->options([
                                'red' => 'Red',
                                'blue' => 'Blue',
                                'green' => 'Green',
                                // Add more color options
                            ])
                            ->required(),
                        Select::make('event_design')
                            ->options([
                                'formal' => 'Formal',
                                'casual' => 'Casual',
                                // Add more event design options
                            ])
                            ->required(),
                    ]),
                Wizard\Step::make('Body Type')
                    ->description('Select your body type')
                    ->schema([
                        Select::make('body_type')
                            ->options([
                                'apple' => 'Apple',
                                'pear' => 'Pear',
                                'hourglass' => 'Hourglass',
                                // Add more body types
                            ])
                            ->required(),
                    ]),
            ])
            ->submitActionLabel('Register');
    }

    public function register()
    {
        // Handle the registration logic here
        // The data is available in $this->form->getState()
        $data = $this->form->getState();

        // 1. Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // 2. Save the personalized recommendation data
        $user->preferences()->create([
            'color_preference' => $data['color_preference'],
            'event_design' => $data['event_design'],
            'body_type' => $data['body_type'],
        ]);

        // 3. Log in the user and redirect
        auth()->login($user);

        return redirect()->route('/');
    }

    public function render()
    {
        return view('livewire.register-wizard');
    }
}
