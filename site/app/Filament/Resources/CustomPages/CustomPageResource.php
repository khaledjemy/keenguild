<?php

namespace App\Filament\Resources\CustomPages;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\CustomPages\Pages\CreateCustomPage;
use App\Filament\Resources\CustomPages\Pages\EditCustomPage;
use App\Filament\Resources\CustomPages\Pages\ListCustomPages;
use App\Models\CustomPage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomPageResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = CustomPage::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentPlus;

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::PAGES;

    protected static ?string $navigationLabel = 'صفحات مضافة';

    protected static ?string $modelLabel = 'صفحة';

    protected static ?string $pluralModelLabel = 'صفحات مضافة';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الصفحة')->schema([
                TextInput::make('slug')->label('الرابط المختصر')->required()->maxLength(80)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true)
                    ->helperText('حروف إنجليزية صغيرة وأرقام وشرطات فقط. رابط الصفحة: /ar/pages/الرابط'),
                Toggle::make('published')->label('نشر الصفحة')->default(false),
            ])->columns(2),
            Section::make('المحتوى العربي')->schema([
                TextInput::make('title_ar')->label('العنوان')->required()->maxLength(160),
                Textarea::make('summary_ar')->label('مقدمة قصيرة')->maxLength(500),
                Textarea::make('body_ar')->label('محتوى الصفحة')->required()->rows(12),
                Textarea::make('meta_description_ar')->label('وصف البحث')->maxLength(180),
            ]),
            Section::make('English content')->schema([
                TextInput::make('title_en')->label('Title')->required()->maxLength(160),
                Textarea::make('summary_en')->label('Short introduction')->maxLength(500),
                Textarea::make('body_en')->label('Page content')->required()->rows(12),
                Textarea::make('meta_description_en')->label('Search description')->maxLength(180),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title_ar')->label('الصفحة')->searchable(),
            TextColumn::make('slug')->label('الرابط')->searchable(),
            IconColumn::make('published')->label('منشورة')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable(),
        ])->recordActions([
            Action::make('preview')->label('معاينة عربية')->url(fn (CustomPage $record): string => route('preview.custom-page', ['locale' => 'ar', 'slug' => $record->slug]))->openUrlInNewTab(),
            Action::make('preview_en')->label('English preview')->url(fn (CustomPage $record): string => route('preview.custom-page', ['locale' => 'en', 'slug' => $record->slug]))->openUrlInNewTab(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomPages::route('/'),
            'create' => CreateCustomPage::route('/create'),
            'edit' => EditCustomPage::route('/{record}/edit'),
        ];
    }
}
