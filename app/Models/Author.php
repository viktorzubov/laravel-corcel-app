<?php

namespace App\Models;

use Corcel\Model\User as Corcel;
use Illuminate\Database\Eloquent\Builder;

class Author extends Corcel
{
    public function scopeByNicename(Builder $query, string $username): Builder
    {
        return $query->where('user_nicename', $username);
    }
}
