<?php

namespace Tests\Sorts;

use Altrntv\EloquentFilter\Filters\EloquentSort;
use Illuminate\Database\Eloquent\Builder;

class UserSort extends EloquentSort
{
    public function name(string $direction): Builder
    {
        return $this->builder
            ->orderBy('name', $direction);
    }

    public function age(string $direction): Builder
    {
        return $this->builder
            ->orderBy('age', $direction);
    }
}
