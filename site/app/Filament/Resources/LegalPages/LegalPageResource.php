<?php

namespace App\Filament\Resources\LegalPages;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\LegalPages\Pages\CreateLegalPage;
use App\Filament\Resources\LegalPages\Pages\EditLegalPage;
use App\Filament\Resources\LegalPages\Pages\ListLegalPages;
use App\Models\LegalPage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LegalPageResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = LegalPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'الخصوصية والشروط';

    protected static ?string $modelLabel = 'صفحة قانونية';

    protected static ?string $pluralModelLabel = 'الخصوصية والشروط';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::PAGES;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->label('نوع الصفحة')->options(['privacy' => 'سياسة الخصوصية', 'terms' => 'الشروط والأحكام'])->required()->unique(ignoreRecord: true),
            TextInput::make('title_ar')->label('العنوان بالعربية')->required()->maxLength(255),
            TextInput::make('title_en')->label('العنوان بالإنجليزية')->required()->maxLength(255),
            Textarea::make('body_ar')->label('النص بالعربية — نص عادي')->required()->rows(14)->columnSpanFull(),
            Textarea::make('body_en')->label('النص بالإنجليزية — نص عادي')->required()->rows(14)->columnSpanFull(),
            Toggle::make('published')->label('منشور للزوار بعد المراجعة')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('type')->label('النوع')->formatStateUsing(fn (string $state): string => $state === 'privacy' ? 'سياسة الخصوصية' : 'الشروط والأحكام'),
            TextColumn::make('title_ar')->label('العنوان')->searchable(),
            IconColumn::make('published')->label('منشور')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable(),
        ])->recordActions([
            Action::make('preview_ar')->label('معاينة عربية')
                ->url(fn (LegalPage $record): string => route('preview.legal', ['locale' => 'ar', 'type' => $record->type]))
                ->openUrlInNewTab(),
            Action::make('preview_en')->label('English preview')
                ->url(fn (LegalPage $record): string => route('preview.legal', ['locale' => 'en', 'type' => $record->type]))
                ->openUrlInNewTab(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalPages::route('/'),
            'create' => CreateLegalPage::route('/create'),
            'edit' => EditLegalPage::route('/{record}/edit'),
        ];
    }
}
