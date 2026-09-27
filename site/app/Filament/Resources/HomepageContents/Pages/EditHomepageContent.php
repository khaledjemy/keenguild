<?php

namespace App\Filament\Resources\HomepageContents\Pages;

use App\Filament\Resources\HomepageContents\HomepageContentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditHomepageContent extends EditRecord
{
    protected static string $resource = HomepageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previewHome')->label('عرض المحتوى الحالي على الرئيسية')
                ->url(route('home'))->openUrlInNewTab(),
        ];
    }
}
