<?php

namespace Tests\Sorts;

use Altrntv\EloquentFilter\Sorts\EloquentSort;

class PostSort extends EloquentSort
{
    public function publishedAt(string $direction): void
    {
        $this->builder->orderBy('published_at', $direction);
    }
}
