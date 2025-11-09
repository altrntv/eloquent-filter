<?php

namespace Altrntv\EloquentFilter\Traits;

use Altrntv\EloquentFilter\Filters\EloquentFilter;
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
trait Filterable
{
    /**
     * @param Builder<TModel> $builder
     * @param array<string, mixed> $parameters
     *
     * @return Builder<TModel>
     */
    protected function scopeFilter(Builder $builder, array $parameters = []): Builder
    {
        $class = $this->eloquentFilterName();

        if (!class_exists($class)) {
            return $builder;
        }

        /** @var EloquentFilter<TModel> $filter */
        $filter = new $class($parameters);

        return $filter->apply($builder);
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @return Builder<TModel>
     *
     * @throws BindingResolutionException
     */
    protected function scopeFilterByRequest(Builder $builder): Builder
    {
        /** @var Request $request */
        $request = Container::getInstance()->make(Request::class);

        /** @var string $key */
        $key = config('eloquent-filter.request_filter_key');
        $parameters = $request->array($key);

        $class = $this->eloquentFilterName();

        if (!class_exists($class)) {
            return $builder;
        }

        /** @var EloquentFilter<TModel> $filter */
        $filter = new $class($parameters);

        return $filter->apply($builder);
    }

    private function eloquentFilterName(): string
    {
        /** @var string $namespace */
        $namespace = config('eloquent-filter.namespace');

        return $namespace . class_basename(self::class) . 'Filter';
    }
}
