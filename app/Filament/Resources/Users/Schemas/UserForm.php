<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\SiteLanguage;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final readonly class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role_id')
                    ->label('Role')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [
                        $role->value => $role->name,
                    ])->all())
                    ->required(),
                Select::make('site_language')
                    ->label('Site language')
                    ->options(collect(SiteLanguage::cases())->mapWithKeys(fn (SiteLanguage $language): array => [
                        $language->value => $language->value,
                    ])->all())
                    ->required(),
            ]);
    }
}
