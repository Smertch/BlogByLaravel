<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SiteTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'alias',
        'language_type',
        'translate',
    ];
}
