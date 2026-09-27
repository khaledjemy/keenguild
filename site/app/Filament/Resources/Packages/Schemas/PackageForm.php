<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->label('الخدمة')->relationship('service', 'title_ar')
                    ->required(),
                TextInput::make('slug')->alphaDash()
                    ->required(),
                TextInput::make('name_ar')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                Textarea::make('description_ar')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                TextInput::make('base_price_egp')->label('سعر البداية بالجنيه')->helperText('يظهر للزوار بصيغة «يبدأ من»؛ ليس متوسطًا رسميًا للسوق ولا عرض سعر نهائيًا. راجع نطاق الباقة قبل النشر.')->numeric()->rules(['integer'])->minValue(fn (Get $get): int => $get('pricing_mode') === 'estimate' ? 1 : 0)
                    ->required(fn (Get $get): bool => $get('pricing_mode') === 'estimate'),
                Select::make('pricing_mode')->label('طريقة التسعير')->options([
                    'estimate' => 'سعر استرشادي', 'custom_quote' => 'عرض سعر مخصص',
                ])
                    ->required()
                    ->live()
                    ->default('estimate'),
                TextInput::make('included_pages')
                    ->required()
                    ->numeric()
                    ->rules(['integer'])
                    ->minValue(0)
                    ->default(0),
                TagsInput::make('included_features')->label('ما تشمله الباقة بالعربية')
                    ->required(fn (Get $get): bool => (bool) $get('published') && $get('pricing_mode') === 'estimate')
                    ->columnSpanFull(),
                TagsInput::make('included_features_en')->label('ما تشمله الباقة بالإنجليزية')
                    ->required(fn (Get $get): bool => (bool) $get('published') && $get('pricing_mode') === 'estimate')
                    ->columnSpanFull(),
                TagsInput::make('excluded_costs')->label('ما لا تشمله الباقة بالعربية')
                    ->columnSpanFull(),
                TagsInput::make('excluded_costs_en')->label('ما لا تشمله الباقة بالإنجليزية')
                    ->columnSpanFull(),
                Toggle::make('published')->label('منشورة للزوار')->default(false)->live(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
