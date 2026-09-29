<?php

namespace App\Filament\Resources\LegalPages\Pages;

use App\Filament\Resources\LegalPages\LegalPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalPage extends CreateRecord
{
    protected static string $resource = LegalPageResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        $type = request()->query('type');
        if (in_array($type, ['privacy', 'terms'], true)) {
            $this->form->fill(['type' => $type]);
        }
    }
}
