<?php

namespace Tests\Filters;

use Altrntv\EloquentFilter\Filters\EloquentFilter;

class PostFilter extends EloquentFilter
{
    protected array $casts = [
        'tag' => 'array',
    ];

    public function tag(array $value): void
    {
        $this->builder->whereIn('tag', $value);
    }

    public function publishedAt(string $value): void
    {
        $this->builder->whereDate('published_at', $value);
    }
}
