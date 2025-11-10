<?php

namespace Tests\Filters;

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends EloquentFilter
{
    protected array $casts = [
        'role' => 'array',
    ];

    protected array $joinParameters = [
        'age' => ['age_from', 'age_to'],
    ];

    public function role(array $value): Builder
    {
        return $this->builder
            ->whereIn('role', $value);
    }

    public function age(string $ageFrom, string $ageTo): Builder
    {
        return $this->builder
            ->whereBetween('age', [$ageFrom, $ageTo]);
    }
}
