<?php

namespace App\Filament\Resources\InquirySettings;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\InquirySettings\Pages\CreateInquirySetting;
use App\Filament\Resources\InquirySettings\Pages\EditInquirySetting;
use App\Filament\Resources\InquirySettings\Pages\ListInquirySettings;
use App\Models\InquirySetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InquirySettingResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = InquirySetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'تشغيل وإيقاف الطلبات';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'تشغيل وإيقاف استقبال الطلبات';

    protected static ?string $pluralModelLabel = 'تشغيل وإيقاف استقبال الطلبات';

    public static function getNavigationUrl(): string
    {
        $setting = InquirySetting::current();

        return $setting
            ? static::getUrl('edit', ['record' => $setting])
            : static::getUrl('create');
    }

    public static function canCreate(): bool
    {
        return ! InquirySetting::query()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('intake_requested')->label('استقبال طلبات المشاريع عبر الموقع')->default(false)
                ->helperText('عند التشغيل يظهر نموذج الطلب إذا توفرت سياسة خصوصية صالحة. عند الإيقاف لا تُقبل طلبات جديدة، وتبقى الطلبات السابقة محفوظة.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            IconColumn::make('intake_requested')->label('التفعيل مطلوب')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquirySettings::route('/'),
            'create' => CreateInquirySetting::route('/create'),
            'edit' => EditInquirySetting::route('/{record}/edit'),
        ];
    }
}
