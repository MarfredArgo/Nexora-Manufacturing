<?php

namespace Modules\Ecommerce\Filament\Resources\Users;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Users\Pages\CreateUser;
use Modules\Ecommerce\Filament\Resources\Users\Pages\EditUser;
use Modules\Ecommerce\Filament\Resources\Users\Pages\ListUsers;
use Modules\Ecommerce\Filament\Resources\Users\Schemas\UserForm;
use Modules\Ecommerce\Filament\Resources\Users\Tables\UsersTable;
use Modules\Ecommerce\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
