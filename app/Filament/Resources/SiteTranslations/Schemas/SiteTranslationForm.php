<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteTranslations\Schemas;

use App\Enums\SiteLanguage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final readonly class SiteTranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('alias')
                    ->required()
                    ->maxLength(190),
                Select::make('language_type')
                    ->options(collect(SiteLanguage::cases())->mapWithKeys(fn (SiteLanguage $language): array => [
                        $language->value => $language->value,
                    ])->all())
                    ->required(),
                Textarea::make('translate')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}
