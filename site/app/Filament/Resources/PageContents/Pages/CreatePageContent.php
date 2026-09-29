<?php

namespace App\Filament\Resources\PageContents\Pages;

use App\Filament\Resources\PageContents\PageContentResource;
use App\Models\PageContent;
use Filament\Resources\Pages\CreateRecord;

class CreatePageContent extends CreateRecord
{
    protected static string $resource = PageContentResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        $key = request()->query('page_key');
        if (is_string($key) && array_key_exists($key, PageContent::PAGES)) {
            $this->form->fill(['page_key' => $key]);
        }
    }
}
