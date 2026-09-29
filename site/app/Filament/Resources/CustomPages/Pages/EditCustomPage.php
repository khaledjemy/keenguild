<?php

namespace App\Filament\Resources\CustomPages\Pages;

use App\Filament\Resources\CustomPages\CustomPageResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomPage extends EditRecord
{
    protected static string $resource = CustomPageResource::class;
}
