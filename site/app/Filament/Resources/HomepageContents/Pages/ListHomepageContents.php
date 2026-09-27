<?php

namespace App\Filament\Resources\HomepageContents\Pages;

use App\Filament\Resources\HomepageContents\HomepageContentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListHomepageContents extends ListRecords
{
    protected static string $resource = HomepageContentResource::class;

    public function getBreadcrumb(): ?string
    {
        return 'إدارة النصوص';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previewHome')->label('معاينة الرئيسية')->url(route('home'))->openUrlInNewTab(),
        ];
    }
}
