<?php

namespace Altrntv\EloquentFilter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface Sort
{
    /**
     * @param Builder<TModel> $builder
     */
    public function __invoke(Builder $builder, string $parameters): void;
}
