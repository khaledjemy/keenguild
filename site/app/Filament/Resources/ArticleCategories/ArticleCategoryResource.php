<?php

namespace App\Filament\Resources\ArticleCategories;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\ArticleCategories\Pages\CreateArticleCategory;
use App\Filament\Resources\ArticleCategories\Pages\EditArticleCategory;
use App\Filament\Resources\ArticleCategories\Pages\ListArticleCategories;
use App\Models\ArticleCategory;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'تصنيفات المقالات';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::CONTENT;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('slug')->label('الرابط المختصر بالإنجليزية')->alphaDash()->required()->unique(ignoreRecord: true)->maxLength(100),
            TextInput::make('name_ar')->label('الاسم بالعربية')->required()->maxLength(120),
            TextInput::make('name_en')->label('الاسم بالإنجليزية')->required()->maxLength(120),
            Textarea::make('description_ar')->label('وصف التصنيف بالعربية')->rows(3)->columnSpanFull(),
            Textarea::make('description_en')->label('وصف التصنيف بالإنجليزية')->rows(3)->columnSpanFull(),
            TextInput::make('sort_order')->label('الترتيب')->integer()->minValue(0)->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name_ar')->label('التصنيف')->searchable(),
            TextColumn::make('slug')->label('الرابط')->searchable(),
            TextColumn::make('articles_count')->counts('articles')->label('المقالات'),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticleCategories::route('/'),
            'create' => CreateArticleCategory::route('/create'),
            'edit' => EditArticleCategory::route('/{record}/edit'),
        ];
    }
}
