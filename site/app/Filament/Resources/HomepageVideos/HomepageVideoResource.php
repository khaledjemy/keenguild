<?php

namespace App\Filament\Resources\HomepageVideos;

use App\Filament\Resources\HomepageVideos\Pages\CreateHomepageVideo;
use App\Filament\Resources\HomepageVideos\Pages\EditHomepageVideo;
use App\Filament\Resources\HomepageVideos\Pages\ListHomepageVideos;
use App\Models\HomepageVideo;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepageVideoResource extends Resource
{
    protected static ?string $model = HomepageVideo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?string $navigationLabel = 'فيديو الرئيسية';

    public static function canCreate(): bool
    {
        return ! HomepageVideo::query()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('video_path')->label('ارفع فيديو MP4')
                ->acceptedFileTypes(['video/mp4'])->maxSize(153600)->disk('public')->directory('homepage-videos')
                ->helperText('حتى 150 MB، حسب حد الرفع في إعدادات السيرفر. اتركه فارغًا إذا ستستخدم رابط MP4 مباشر.')
                ->required(fn (Get $get): bool => (bool) $get('published') && blank($get('video_url'))),
            TextInput::make('video_url')->label('أو رابط فيديو MP4 مباشر')
                ->url()->rule('starts_with:https://')
                ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filled($value) && ! str_ends_with(strtolower((string) parse_url((string) $value, PHP_URL_PATH)), '.mp4')) {
                        $fail('أدخل رابط HTTPS مباشر ينتهي باسم ملف MP4، وليس رابط صفحة فيديو.');
                    }
                })->maxLength(2048)
                ->helperText('رابط HTTPS مباشر لملف MP4؛ روابط صفحات YouTube لا تعمل داخل مشغل الفيديو.')
                ->required(fn (Get $get): bool => (bool) $get('published') && blank($get('video_path'))),
            Toggle::make('published')->label('اعتماد الفيديو للعرض')->default(false)->live()
                ->helperText('الفيديو الحالي يظل ظاهرًا حتى تعتمد ملفًا أو رابطًا جديدًا. ألغِ الاعتماد للعودة إليه.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('video_path')->label('الملف')->limit(45)->placeholder('—'),
            TextColumn::make('video_url')->label('الرابط')->limit(50)->placeholder('—'),
            IconColumn::make('published')->label('معتمد')->boolean(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepageVideos::route('/'),
            'create' => CreateHomepageVideo::route('/create'),
            'edit' => EditHomepageVideo::route('/{record}/edit'),
        ];
    }
}
