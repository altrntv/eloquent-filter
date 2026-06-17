<?php

namespace Tests\Sorts;

use Altrntv\EloquentFilter\Sorts\EloquentSort;

class UserSort extends EloquentSort
{
    public function name(string $direction): void
    {
        $this->builder->orderBy('name', $direction);
    }

    public function age(string $direction): void
    {
        $this->builder->orderBy('age', $direction);
    }
}
