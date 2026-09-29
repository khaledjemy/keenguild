<?php

namespace App\Filament\Resources\QuoteRequests;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\QuoteRequests\Pages\EditQuoteRequest;
use App\Filament\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Models\QuoteRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::CONTACT;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'طلبات عرض السعر';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->disabled(),
            TextInput::make('email')->label('البريد')->disabled(),
            TextInput::make('phone')->label('الهاتف')->disabled(),
            TextInput::make('package_id')->label('رقم الباقة')->disabled(),
            Textarea::make('project_brief')->label('وصف المشروع')->rows(8)->disabled()->columnSpanFull(),
            Select::make('status')->label('حالة المتابعة')->options([
                'new' => 'جديد', 'reviewing' => 'قيد المراجعة',
                'contacted' => 'تم التواصل', 'closed' => 'مغلق',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('الاسم')->searchable(),
            TextColumn::make('email')->label('البريد')->searchable(),
            TextColumn::make('package.name_ar')->label('الباقة'),
            TextColumn::make('status')->label('الحالة')->badge(),
            TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime()->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make()->label('حذف الطلب')->requiresConfirmation(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'edit' => EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
