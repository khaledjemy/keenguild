<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previewPricing')->label('معاينة الأسعار المسودة')
                ->url(route('preview.pricing', ['locale' => 'ar']))->openUrlInNewTab(),
            Action::make('previewPricingEn')->label('English preview')
                ->url(route('preview.pricing', ['locale' => 'en']))->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
