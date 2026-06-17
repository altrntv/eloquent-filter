<?php

namespace Tests\Filters;

use Altrntv\EloquentFilter\Filters\EloquentFilter;

class UserFilter extends EloquentFilter
{
    protected array $casts = [
        'role' => 'array',
    ];

    protected array $joinParameters = [
        'age' => ['age_from', 'age_to'],
    ];

    public function role(array $value): void
    {
        $this->builder->whereIn('role', $value);
    }

    public function age(int|string $ageFrom, int|string $ageTo): void
    {
        $this->builder->whereBetween('age', [$ageFrom, $ageTo]);
    }
}
