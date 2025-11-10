<?php

namespace Tests\Filters;

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class PostFilter extends EloquentFilter
{
    protected array $casts = [
        'tag' => 'array',
    ];

    public function tag(array $value): Builder
    {

        return $this->builder
            ->whereIn('tag', $value);
    }

    public function publishedAt(string $value): Builder
    {
        return $this->builder
            ->whereDate('published_at', $value);
    }
}
