<?php

namespace Altrntv\EloquentFilter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface Filter
{
    /**
     * @param Builder $builder
     * @param array<string, mixed> $parameters
     *
     * @return void
     */
    public function __invoke(Builder $builder, array $parameters = []): void;
}
