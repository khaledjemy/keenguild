<?php

namespace App\Filament\Resources\PriceOptions;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\PriceOptions\Pages\CreatePriceOption;
use App\Filament\Resources\PriceOptions\Pages\EditPriceOption;
use App\Filament\Resources\PriceOptions\Pages\ListPriceOptions;
use App\Filament\Resources\PriceOptions\Schemas\PriceOptionForm;
use App\Filament\Resources\PriceOptions\Tables\PriceOptionsTable;
use App\Models\PriceOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PriceOptionResource extends Resource
{
    protected static ?string $model = PriceOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'إضافات الأسعار';

    protected static ?string $modelLabel = 'إضافة سعر';

    protected static ?string $pluralModelLabel = 'إضافات الأسعار';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::PRICING;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PriceOptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceOptionsTable::configure($table);
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
            'index' => ListPriceOptions::route('/'),
            'create' => CreatePriceOption::route('/create'),
            'edit' => EditPriceOption::route('/{record}/edit'),
        ];
    }
}
