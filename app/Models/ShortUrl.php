<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string url
 * @property string short_url
 */
class ShortUrl extends Model
{
    use HasFactory;

    public static function shortenUrl(string $urlToShort): string
    {
        return substr(Str::of($urlToShort)->pipe('md5'), 0, 6);
    }
}
