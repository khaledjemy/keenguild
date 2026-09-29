<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\SeoSettings\SeoSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeoSettings extends ListRecords
{
    protected static string $resource = SeoSettingResource::class;

    public function mount(): void
    {
        parent::mount();
        $this->redirect(SiteSettings::getUrl());
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->visible(fn (): bool => SeoSettingResource::canCreate())];
    }
}
