<?php

namespace App\Filament\Resources\PriceOptions\Pages;

use App\Filament\Resources\PriceOptions\PriceOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPriceOption extends EditRecord
{
    protected static string $resource = PriceOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
