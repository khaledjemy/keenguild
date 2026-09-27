<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')->label('الرابط المختصر')->alphaDash()->required(),
                Select::make('category')->label('المجال')->options([
                    'web' => 'المواقع والمتاجر', 'software' => 'البرمجيات',
                    'design' => 'التصميم', 'support' => 'الدعم',
                ])->required()
                    ->default('web'),
                TextInput::make('title_ar')->label('اسم الخدمة بالعربية')
                    ->required(),
                TextInput::make('title_en')->label('اسم الخدمة بالإنجليزية')
                    ->required(),
                Textarea::make('summary_ar')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('summary_en')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('body_ar')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('body_en')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Toggle::make('featured')
                    ->required(),
                Toggle::make('featured_on_home')->label('عرض بطاقة في الرئيسية')->default(false)
                    ->helperText('تظهر أول أربع خدمات منشورة حسب الترتيب في قسم الخدمات بالرئيسية.'),
                Toggle::make('published')->label('منشورة للزوار')->default(false)->live(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
