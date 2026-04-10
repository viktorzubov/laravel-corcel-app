<?php

namespace App\Models;

use Corcel\Model\Taxonomy as Corcel;

class Tag extends Corcel
{
    protected static function booted(): void
    {
        static::addGlobalScope('tag', fn ($q) => $q->where('taxonomy', 'post_tag'));
    }
}
