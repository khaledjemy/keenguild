<?php

namespace App\Filament\Resources\PriceOptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceOptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package.name_ar')->label('الباقة')->searchable(),
                TextColumn::make('code')->label('الكود')->searchable(),
                TextColumn::make('label_ar')->label('الإضافة')->searchable(),
                TextColumn::make('label_en')->label('بالإنجليزية')->searchable(),
                TextColumn::make('calculation_type')->label('طريقة الحساب')->searchable(),
                TextColumn::make('amount_egp')->label('القيمة (ج.م)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('percent')->label('النسبة %')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_quantity')->label('أقصى كمية')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('published')->label('منشورة')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
