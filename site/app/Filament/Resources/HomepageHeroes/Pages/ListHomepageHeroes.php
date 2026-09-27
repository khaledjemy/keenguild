<?php

namespace App\Filament\Resources\HomepageHeroes\Pages;

use App\Filament\Resources\HomepageHeroes\HomepageHeroResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomepageHeroes extends ListRecords
{
    protected static string $resource = HomepageHeroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publicHome')->label('الرئيسية المنشورة')->url(route('home'))->openUrlInNewTab(),
            CreateAction::make()->visible(fn (): bool => HomepageHeroResource::canCreate()),
        ];
    }
}
