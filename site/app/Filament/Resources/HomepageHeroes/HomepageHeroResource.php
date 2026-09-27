<?php

namespace App\Filament\Resources\HomepageHeroes;

use App\Filament\Resources\HomepageHeroes\Pages\CreateHomepageHero;
use App\Filament\Resources\HomepageHeroes\Pages\EditHomepageHero;
use App\Filament\Resources\HomepageHeroes\Pages\ListHomepageHeroes;
use App\Models\HomepageHero;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepageHeroResource extends Resource
{
    protected static ?string $model = HomepageHero::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'مقدمة الرئيسية';

    public static function canCreate(): bool
    {
        return ! HomepageHero::query()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('kicker_ar')->label('النص الصغير بالعربية')->required()->maxLength(90),
            TextInput::make('kicker_en')->label('النص الصغير بالإنجليزية')->required()->maxLength(90),
            TextInput::make('headline_ar')->label('السطر الأول بالعربية')->required()->maxLength(45),
            TextInput::make('headline_en')->label('السطر الأول بالإنجليزية')->required()->maxLength(45),
            TextInput::make('accent_ar')->label('السطر الملوّن بالعربية')->required()->maxLength(28),
            TextInput::make('accent_en')->label('السطر الملوّن بالإنجليزية')->required()->maxLength(28),
            Textarea::make('description_ar')->label('الوصف بالعربية')->required()->maxLength(210)->columnSpanFull(),
            Textarea::make('description_en')->label('الوصف بالإنجليزية')->required()->maxLength(210)->columnSpanFull(),
            Toggle::make('published')->label('اعتماد النص وعرضه في الرئيسية')->helperText('المسودة لا تغيّر التصميم الحالي. راجع النصين على شاشة الموبايل قبل الاعتماد.')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('headline_ar')->label('العنوان بالعربية'),
            IconColumn::make('published')->label('معتمد')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([
            Action::make('preview_ar')->label('معاينة عربية')
                ->url(fn (HomepageHero $record): string => route('preview.home.hero', ['locale' => 'ar', 'hero' => $record]))
                ->openUrlInNewTab(),
            Action::make('preview_en')->label('English preview')
                ->url(fn (HomepageHero $record): string => route('preview.home.hero', ['locale' => 'en', 'hero' => $record]))
                ->openUrlInNewTab(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepageHeroes::route('/'),
            'create' => CreateHomepageHero::route('/create'),
            'edit' => EditHomepageHero::route('/{record}/edit'),
        ];
    }
}
