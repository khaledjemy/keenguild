<?php

namespace App\Filament\Resources\NavigationItems;

use App\Filament\Resources\NavigationItems\Pages\CreateNavigationItem;
use App\Filament\Resources\NavigationItems\Pages\EditNavigationItem;
use App\Filament\Resources\NavigationItems\Pages\ListNavigationItems;
use App\Models\NavigationItem;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NavigationItemResource extends Resource
{
    protected static ?string $model = NavigationItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $navigationLabel = 'المنيو والفوتر';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('location')->label('مكان الظهور')->options([
                'header' => 'الشريط العلوي', 'overlay' => 'القائمة المفتوحة', 'footer' => 'الفوتر',
            ])->required(),
            Select::make('target')->label('الوجهة الداخلية')->options(NavigationItem::TARGETS)->required()
                ->helperText('الروابط تُبنى تلقائيًا بالعربية والإنجليزية. أقسام الرئيسية تنتقل إلى موضعها داخل الصفحة.'),
            TextInput::make('label_ar')->label('العنوان بالعربية')->required()->maxLength(80),
            TextInput::make('label_en')->label('العنوان بالإنجليزية')->required()->maxLength(80),
            TextInput::make('sort_order')->label('الترتيب')->numeric()->minValue(0)->maxValue(65535)->required()->default(0),
            Toggle::make('published')->label('ظاهر للزوار')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('location')->label('المكان')->badge()->sortable(),
            TextColumn::make('label_ar')->label('العنوان')->searchable(),
            TextColumn::make('target')->label('الوجهة'),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
            IconColumn::make('published')->label('ظاهر')->boolean(),
        ])->defaultSort('sort_order')->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNavigationItems::route('/'),
            'create' => CreateNavigationItem::route('/create'),
            'edit' => EditNavigationItem::route('/{record}/edit'),
        ];
    }
}
