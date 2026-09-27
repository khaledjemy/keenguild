<?php

namespace App\Filament\Resources\PriceOptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PriceOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('package_id')
                    ->label('الباقة')->relationship('package', 'name_ar')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('label_ar')
                    ->required(),
                TextInput::make('label_en')
                    ->required(),
                Select::make('calculation_type')->label('طريقة الحساب')->options([
                    'fixed' => 'مبلغ ثابت', 'per_unit' => 'لكل وحدة', 'percent' => 'نسبة من سعر الباقة',
                ])
                    ->required()
                    ->live()
                    ->default('fixed'),
                TextInput::make('amount_egp')
                    ->required()
                    ->numeric()
                    ->rules(['integer'])
                    ->minValue(fn (Get $get): int => (bool) $get('published') && $get('calculation_type') !== 'percent' ? 1 : 0)
                    ->default(0),
                TextInput::make('percent')
                    ->required()
                    ->numeric()
                    ->rules(['integer'])
                    ->minValue(fn (Get $get): int => (bool) $get('published') && $get('calculation_type') === 'percent' ? 1 : 0)
                    ->maxValue(100)
                    ->default(0),
                TextInput::make('max_quantity')
                    ->required()
                    ->numeric()
                    ->rules(['integer'])
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(1),
                Toggle::make('published')->label('متاحة في الحاسبة')->helperText('اتركها مسودة حتى تعتمد قيمة الإضافة؛ المبلغ أو النسبة المنشورة يجب أن تكون أكبر من صفر.')->default(false)->live(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
