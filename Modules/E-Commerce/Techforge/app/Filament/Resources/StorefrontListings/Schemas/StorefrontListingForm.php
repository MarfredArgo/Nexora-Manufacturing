<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Support\EcommerceClientContext;

class StorefrontListingForm
{
    public static function configure(Schema $schema): Schema
    {
        $clientId = (int) app(EcommerceClientContext::class)->clientId();
        $bomOptions = $clientId
            ? DB::connection('manufacturing')->table('product_boms')
                ->where('client_id', $clientId)->where('status', 'active')
                ->orderBy('name')->get()
                ->mapWithKeys(fn ($bom) => [$bom->id => $bom->sku.' · '.$bom->name])->all()
            : [];

        return $schema->components([
            Select::make('bom_id')->label('Bill of Materials')->options($bomOptions)->required()->searchable(),
            TextInput::make('sku')->required()->maxLength(100),
            TextInput::make('name')->required()->maxLength(160),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('price')->numeric()->prefix('₱')->required(),
            FileUpload::make('image_url')->disk('public')->directory('storefront-listings')->image(),
            Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'])->default('draft')->required(),
        ]);
    }
}
