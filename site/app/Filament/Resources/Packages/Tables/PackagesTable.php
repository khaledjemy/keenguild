<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.title_ar')->label('الخدمة')->searchable(),
                TextColumn::make('slug')->label('الرابط')->searchable(),
                TextColumn::make('name_ar')->label('الباقة')->searchable(),
                TextColumn::make('name_en')
                    ->searchable(),
                TextColumn::make('base_price_egp')->label('سعر البداية (ج.م)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pricing_mode')->label('طريقة التسعير')
                    ->searchable(),
                TextColumn::make('included_pages')->label('الصفحات المشمولة')
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
