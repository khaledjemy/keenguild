<?php

namespace App\Filament\Resources\ContactChannels;

use App\Filament\AdminNavigationGroups;
use App\Filament\Resources\ContactChannels\Pages\CreateContactChannel;
use App\Filament\Resources\ContactChannels\Pages\EditContactChannel;
use App\Filament\Resources\ContactChannels\Pages\ListContactChannels;
use App\Models\ContactChannel;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactChannelResource extends Resource
{
    protected static ?string $model = ContactChannel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'قنوات التواصل';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::CONTACT;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('platform')->label('المنصة')->options(array_combine(ContactChannel::PLATFORMS, ContactChannel::PLATFORMS))
                ->required()->unique(ignoreRecord: true),
            TextInput::make('url')->label('الرابط الرسمي أو البريد')
                ->helperText('يمكن تركه فارغًا كمسودة. عند النشر، اكتب رابط HTTPS لحساب KeenGuild الفعلي، أو البريد الإلكتروني لقناة Email.')
                ->rule(fn (Get $get) => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                    if (! (bool) $get('published')) {
                        return;
                    }

                    $channel = new ContactChannel([
                        'platform' => (string) $get('platform'),
                        'url' => is_string($value) ? trim($value) : '',
                        'published' => true,
                    ]);
                    if ($channel->publicUrl() === null) {
                        $fail('لا يمكن نشر القناة قبل إدخال رابط الحساب الرسمي الصحيح أو بريد إلكتروني صالح.');
                    }
                })
                ->required(fn (Get $get): bool => (bool) $get('published'))->maxLength(2048)->columnSpanFull(),
            Toggle::make('published')->label('رابط رسمي مؤكد ومنشور')->default(false)->live(),
            TextInput::make('sort_order')->label('الترتيب')->integer()->minValue(0)->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('platform')->label('المنصة')->searchable(),
            TextColumn::make('url')->label('الرابط')->limit(55),
            IconColumn::make('published')->label('منشور')->boolean(),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactChannels::route('/'),
            'create' => CreateContactChannel::route('/create'),
            'edit' => EditContactChannel::route('/{record}/edit'),
        ];
    }
}
