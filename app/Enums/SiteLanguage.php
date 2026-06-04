<?php

declare(strict_types=1);

namespace App\Enums;

enum SiteLanguage: string
{
    case EN = 'EN';
    case ES = 'ES';
    case DE = 'DE';
    case UA = 'UA';

    public function label(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::ES => 'Español',
            self::DE => 'Deutsch',
            self::UA => 'Українська',
        };
    }
}
