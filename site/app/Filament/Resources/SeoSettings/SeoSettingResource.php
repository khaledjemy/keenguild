<?php

namespace App\Filament\Resources\SeoSettings;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\SeoSettings\Pages\CreateSeoSetting;
use App\Filament\Resources\SeoSettings\Pages\EditSeoSetting;
use App\Filament\Resources\SeoSettings\Pages\ListSeoSettings;
use App\Models\SeoSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoSettingResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = SeoSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'الفهرسة وSEO';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return ! SeoSetting::query()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('home_title_ar')->label('عنوان الرئيسية بالعربية')->maxLength(70),
            TextInput::make('home_title_en')->label('عنوان الرئيسية بالإنجليزية')->maxLength(70),
            Textarea::make('default_description_ar')->label('الوصف الافتراضي بالعربية')->maxLength(180),
            Textarea::make('default_description_en')->label('الوصف الافتراضي بالإنجليزية')->maxLength(180),
            FileUpload::make('social_image_path')->label('صورة مشاركة الرابط')->image()->disk('public')->directory('seo')->maxSize(4096),
            Toggle::make('allow_indexing')->label('السماح بالفهرسة')->default(true)
                ->helperText('إيقافه يضع noindex على الصفحات ويمنع الزحف عبر robots.txt. لا يضمن حذف نتائج مفهرسة سابقًا.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('home_title_ar')->label('عنوان الرئيسية')->placeholder('الافتراضي'),
            IconColumn::make('allow_indexing')->label('الفهرسة مسموحة')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoSettings::route('/'),
            'create' => CreateSeoSetting::route('/create'),
            'edit' => EditSeoSetting::route('/{record}/edit'),
        ];
    }
}
