<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use BackedEnum;
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

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = 'الأسئلة الشائعة';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('question_ar')->label('السؤال بالعربية')->required()->maxLength(255),
            TextInput::make('question_en')->label('السؤال بالإنجليزية')->required()->maxLength(255),
            Textarea::make('answer_ar')->label('الإجابة بالعربية')->required()->columnSpanFull(),
            Textarea::make('answer_en')->label('الإجابة بالإنجليزية')->required()->columnSpanFull(),
            Toggle::make('published')->label('منشور للزوار')->default(false),
            TextInput::make('sort_order')->label('الترتيب')->integer()->minValue(0)->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question_ar')->label('السؤال بالعربية')->searchable(),
            TextColumn::make('question_en')->label('السؤال بالإنجليزية')->searchable(),
            IconColumn::make('published')->label('منشور')->boolean(),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
