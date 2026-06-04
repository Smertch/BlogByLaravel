<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteTranslations\Pages;

use App\Filament\Resources\SiteTranslations\SiteTranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListSiteTranslations extends ListRecords
{
    protected static string $resource = SiteTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
