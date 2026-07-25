<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages\CreateStorefrontListing;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages\EditStorefrontListing;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages\ListStorefrontListings;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\Schemas\StorefrontListingForm;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\Tables\StorefrontListingsTable;
use Modules\Ecommerce\Models\StorefrontListing;

class StorefrontListingResource extends Resource
{
    protected static ?string $model = StorefrontListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Storefront Listings';

    protected static \UnitEnum|string|null $navigationGroup = 'Storefront';

    public static function form(Schema $schema): Schema
    {
        return StorefrontListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StorefrontListingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStorefrontListings::route('/'),
            'create' => CreateStorefrontListing::route('/create'),
            'edit' => EditStorefrontListing::route('/{record}/edit'),
        ];
    }
}
