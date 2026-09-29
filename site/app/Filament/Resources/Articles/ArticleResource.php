<?php

namespace App\Filament\Resources\Articles;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'المقالات';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::CONTENT;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('article_category_id')->label('التصنيف')->relationship('category', 'name_ar')->searchable()->preload()
                ->helperText('أنشئ التصنيفات من قسم «تصنيفات المقالات».')->required(fn (Get $get): bool => (bool) $get('published')),
            TextInput::make('slug')->label('الرابط المختصر')->alphaDash()->required()->unique(ignoreRecord: true)->maxLength(255),
            TextInput::make('title_ar')->label('العنوان بالعربية')->required()->maxLength(255),
            TextInput::make('title_en')->label('العنوان بالإنجليزية')->required()->maxLength(255),
            Textarea::make('summary_ar')->label('الملخص بالعربية')->required()->columnSpanFull(),
            Textarea::make('summary_en')->label('الملخص بالإنجليزية')->required()->columnSpanFull(),
            Textarea::make('body_ar')->label('المقال بالعربية — نص عادي')->required()->rows(12)->columnSpanFull(),
            Textarea::make('body_en')->label('المقال بالإنجليزية — نص عادي')->required()->rows(12)->columnSpanFull(),
            FileUpload::make('cover_path')->label('صورة الغلاف')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(4096)->disk('public')->directory('articles'),
            Toggle::make('published')->label('منشور للزوار')->default(false)->live(),
            DateTimePicker::make('published_at')->label('موعد النشر')->required(fn (Get $get): bool => (bool) $get('published')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title_ar')->label('العنوان بالعربية')->searchable(),
            TextColumn::make('category.name_ar')->label('التصنيف'),
            TextColumn::make('slug')->label('الرابط')->searchable(),
            IconColumn::make('published')->label('منشور')->boolean(),
            TextColumn::make('published_at')->label('موعد النشر')->dateTime()->sortable(),
        ])->recordActions([
            Action::make('preview_ar')->label('معاينة عربية')
                ->url(fn (Article $record): string => route('preview.article', ['locale' => 'ar', 'slug' => $record->slug]))
                ->openUrlInNewTab(),
            Action::make('preview_en')->label('English preview')
                ->url(fn (Article $record): string => route('preview.article', ['locale' => 'en', 'slug' => $record->slug]))
                ->openUrlInNewTab(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
