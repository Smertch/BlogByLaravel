<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteTranslations\Pages;

use App\Filament\Resources\SiteTranslations\SiteTranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditSiteTranslation extends EditRecord
{
    protected static string $resource = SiteTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
