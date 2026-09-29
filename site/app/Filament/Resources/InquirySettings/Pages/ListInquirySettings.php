<?php

namespace App\Filament\Resources\InquirySettings\Pages;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\InquirySettings\InquirySettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInquirySettings extends ListRecords
{
    protected static string $resource = InquirySettingResource::class;

    public function mount(): void
    {
        parent::mount();
        $this->redirect(SiteSettings::getUrl());
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->visible(fn (): bool => InquirySettingResource::canCreate())];
    }
}
