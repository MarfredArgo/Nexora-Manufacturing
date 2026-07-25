<?php

namespace Modules\Ecommerce\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('username')
                    ->required(),
                TextInput::make('password')
                    ->password(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('role')
                    ->required()
                    ->default('root_admin'),
                TextInput::make('client_id')
                    ->numeric(),
                TextInput::make('role_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('gender'),
                DatePicker::make('dob'),
                TextInput::make('provider'),
                TextInput::make('provider_id'),
            ]);
    }
}
