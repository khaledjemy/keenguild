<?php

namespace App\Filament\Resources\PageContents;

use App\Filament\Resources\PageContents\Pages\CreatePageContent;
use App\Filament\Resources\PageContents\Pages\EditPageContent;
use App\Filament\Resources\PageContents\Pages\ListPageContents;
use App\Models\PageContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'محتوى الصفحات';

    public static function form(Schema $schema): Schema
    {
        $processFields = [];
        foreach (['about' => 'من نحن', 'pricing' => 'الأسعار'] as $prefix => $label) {
            for ($i = 1; $i <= 3; $i++) {
                foreach (['title' => 'عنوان', 'description' => 'وصف'] as $part => $partLabel) {
                    foreach (['ar' => 'عربي', 'en' => 'English'] as $locale => $language) {
                        $processFields[] = TextInput::make($prefix.'_'.$i.'_'.$part.'_'.$locale)
                            ->label($label.' — خطوة '.$i.' — '.$partLabel.' — '.$language)
                            ->maxLength($part === 'title' ? 100 : 260);
                    }
                }
            }
        }

        return $schema->components([
            Select::make('page_key')->label('الصفحة')->options(PageContent::PAGES)->required()
                ->unique(ignoreRecord: true)->disabledOn('edit'),
            TextInput::make('heading_ar')->label('العنوان بالعربية')->maxLength(120),
            TextInput::make('heading_en')->label('العنوان بالإنجليزية')->maxLength(120),
            Textarea::make('intro_ar')->label('مقدمة الصفحة بالعربية')->maxLength(400),
            Textarea::make('intro_en')->label('مقدمة الصفحة بالإنجليزية')->maxLength(400),
            Textarea::make('meta_description_ar')->label('وصف البحث بالعربية')->maxLength(180),
            Textarea::make('meta_description_en')->label('وصف البحث بالإنجليزية')->maxLength(180),
            Section::make('خطوات من نحن وطريقة التسعير')
                ->description('املأ حقول الصفحة التي تحررها فقط. الحقول الفارغة تُبقي النص الحالي.')
                ->schema($processFields)->statePath('extra_copy')->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('page_key')->label('الصفحة')->formatStateUsing(fn (string $state): string => PageContent::PAGES[$state] ?? $state),
            TextColumn::make('heading_ar')->label('العنوان')->placeholder('الافتراضي'),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([
            Action::make('preview')->label('معاينة عربية')
                ->url(fn (PageContent $record): string => route($record->page_key, ['locale' => 'ar']))
                ->openUrlInNewTab(),
            Action::make('preview_en')->label('English preview')
                ->url(fn (PageContent $record): string => route($record->page_key, ['locale' => 'en']))
                ->openUrlInNewTab(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageContents::route('/'),
            'create' => CreatePageContent::route('/create'),
            'edit' => EditPageContent::route('/{record}/edit'),
        ];
    }
}
