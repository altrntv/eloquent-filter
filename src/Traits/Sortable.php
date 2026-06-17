<?php

namespace Altrntv\EloquentFilter\Traits;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Altrntv\EloquentFilter\Sorts\EloquentSort;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @template TModel of Model
 *
 * @mixin Model
 */
trait Sortable
{
    /**
     * @param Builder<TModel> $builder
     */
    public function scopeSort(Builder $builder, string $parameters): void
    {
        $class = $this->eloquentSortName();

        if (!class_exists($class)) {
            return;
        }

        /** @var EloquentSort<TModel> $sort */
        $sort = app()->make($class);

        $sort($builder, $parameters);
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @throws BindingResolutionException
     */
    public function scopeSortByRequest(Builder $builder): void
    {
        /** @var Request $request */
        $request = Container::getInstance()->make(Request::class);

        $parameters = $request->has(ConfigHelper::requestSortKey())
            ? $request->string(ConfigHelper::requestSortKey())
            : null;

        if (is_null($parameters)) {
            return;
        }

        $class = $this->eloquentSortName();

        if (!class_exists($class)) {
            return;
        }

        /** @var EloquentSort<TModel> $sort */
        $sort = app()->make($class);

        $sort($builder, (string) $parameters);
    }

    private function eloquentSortName(): string
    {
        return ConfigHelper::sortNamespace() . class_basename(static::class) . 'Sort';
    }
}
