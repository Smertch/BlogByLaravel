<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteTranslations\Pages;

use App\Filament\Resources\SiteTranslations\SiteTranslationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSiteTranslation extends CreateRecord
{
    protected static string $resource = SiteTranslationResource::class;
}
