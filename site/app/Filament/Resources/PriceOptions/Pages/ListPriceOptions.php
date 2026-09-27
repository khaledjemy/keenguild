<?php

namespace App\Filament\Resources\PriceOptions\Pages;

use App\Filament\Resources\PriceOptions\PriceOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPriceOptions extends ListRecords
{
    protected static string $resource = PriceOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
