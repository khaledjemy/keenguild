<?php

namespace App\Filament\Resources\PricingSettings;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\PricingSettings\Pages\EditPricingSetting;
use App\Filament\Resources\PricingSettings\Pages\ListPricingSettings;
use App\Filament\Resources\PricingSettings\Schemas\PricingSettingForm;
use App\Filament\Resources\PricingSettings\Tables\PricingSettingsTable;
use App\Models\PricingSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PricingSettingResource extends Resource
{
    protected static ?string $model = PricingSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'إعدادات التسعير';

    protected static ?string $modelLabel = 'إعداد تسعير';

    protected static ?string $pluralModelLabel = 'إعدادات التسعير';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::PRICING;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PricingSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricingSettingsTable::configure($table);
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
            'index' => ListPricingSettings::route('/'),
            'edit' => EditPricingSetting::route('/{record}/edit'),
        ];
    }
}
