<?php

namespace Altrntv\EloquentFilter\Traits;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Altrntv\EloquentFilter\Filters\EloquentSort;
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
     *
     * @return Builder<TModel>
     */
    public function scopeSort(Builder $builder, string $parameters): Builder
    {
        $class = $this->eloquentSortName();

        if (!class_exists($class)) {
            return $builder;
        }

        /** @var EloquentSort<TModel> $sort */
        $sort = new $class($parameters);

        return $sort->apply($builder);
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @return Builder<TModel>
     *
     * @throws BindingResolutionException
     */
    public function scopeSortByRequest(Builder $builder): Builder
    {
        /** @var Request $request */
        $request = Container::getInstance()->make(Request::class);

        $parameters = $request->has(ConfigHelper::requestSortKey())
            ? $request->string(ConfigHelper::requestSortKey())
            : null;

        if (is_null($parameters)) {
            return $builder;
        }

        $class = $this->eloquentSortName();

        if (!class_exists($class)) {
            return $builder;
        }

        /** @var EloquentSort<TModel> $sort */
        $sort = new $class($parameters);

        return $sort->apply($builder);
    }

    private function eloquentSortName(): string
    {
        return ConfigHelper::sortNamespace() . class_basename(self::class) . 'Sort';
    }
}
