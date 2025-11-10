<?php

namespace Tests\Sorts;

use Altrntv\EloquentFilter\Filters\EloquentSort;
use Illuminate\Database\Eloquent\Builder;

class PostSort extends EloquentSort
{
    public function publishedAt(string $direction): Builder
    {
        return $this->builder
            ->orderBy('published_at', $direction);
    }
}
