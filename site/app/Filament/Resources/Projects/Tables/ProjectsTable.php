<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('title_ar')
                    ->searchable(),
                TextColumn::make('title_en')
                    ->searchable(),
                TextColumn::make('project_type')->label('نوع العرض')->badge(),
                TextColumn::make('cover_path')
                    ->searchable(),
                TextColumn::make('demo_url')
                    ->searchable(),
                TextColumn::make('demo_status')
                    ->searchable(),
                IconColumn::make('featured')
                    ->boolean(),
                IconColumn::make('featured_in_demos')->label('تجارب الرئيسية')->boolean(),
                IconColumn::make('published')
                    ->label('طلب النشر')->boolean(),
                IconColumn::make('display_permission_confirmed')->label('إذن العرض')->boolean(),
                TextColumn::make('public_status')->label('الظهور الفعلي')->state(fn (Project $record): string => ! $record->published
                    ? 'مسودة'
                    : ($record->project_type === 'client' && ! $record->display_permission_confirmed
                        ? 'محجوب حتى تأكيد الإذن'
                        : ($record->isPubliclyVisible() ? 'ظاهر للزوار' : 'محجوب حتى اكتمال النصوص')))->badge(),
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
                Action::make('preview')->label('معاينة عربية')
                    ->url(fn (Project $record): string => route('preview.project', ['locale' => 'ar', 'slug' => $record->slug]))
                    ->openUrlInNewTab(),
                Action::make('preview_en')->label('English preview')
                    ->url(fn (Project $record): string => route('preview.project', ['locale' => 'en', 'slug' => $record->slug]))
                    ->openUrlInNewTab(),
                Action::make('tour')->label('جولة الصور بالعربية')
                    ->url(fn (Project $record): string => route('preview.project.tour', ['locale' => 'ar', 'slug' => $record->slug]))
                    ->visible(fn (Project $record): bool => $record->tourImages() !== [])
                    ->openUrlInNewTab(),
                Action::make('tour_en')->label('English image tour')
                    ->url(fn (Project $record): string => route('preview.project.tour', ['locale' => 'en', 'slug' => $record->slug]))
                    ->visible(fn (Project $record): bool => $record->tourImages() !== [])
                    ->openUrlInNewTab(),
                Action::make('live_demo')->label('فتح الديمو')
                    ->url(fn (Project $record): string => (string) $record->demo_url)
                    ->visible(fn (Project $record): bool => $record->hasLiveDemo())
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
