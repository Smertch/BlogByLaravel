<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: int
{
    case User = 1;
    case Admin = 2;
}
