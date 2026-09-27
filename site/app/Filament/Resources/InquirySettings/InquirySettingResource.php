<?php

namespace App\Filament\Resources\InquirySettings;

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
    protected static ?string $model = InquirySetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'استقبال طلبات المشاريع';

    protected static ?string $modelLabel = 'إعداد استقبال الطلبات';

    public static function canCreate(): bool
    {
        return ! InquirySetting::query()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('intake_requested')->label('تفعيل نموذج طلب المشروع')->default(false)
                ->helperText('لا يستقبل النموذج بيانات إلا بعد نشر سياسة خصوصية عربية وإنجليزية أو ضبط رابط خصوصية HTTPS صالح. الطلبات تُحفظ في قسم طلبات عروض الأسعار؛ روابط التواصل الاجتماعي اختيارية.'),
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
