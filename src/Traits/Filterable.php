<?php

namespace Altrntv\EloquentFilter\Traits;

use Altrntv\EloquentFilter\Config\ConfigHelper;
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
     */
    public function scopeFilter(Builder $builder, array $parameters = []): void
    {
        $class = $this->eloquentFilterName();

        if (!class_exists($class)) {
            return;
        }

        /** @var EloquentFilter<TModel> $filter */
        $filter = app()->make($class);

        $filter($builder, $parameters);
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @throws BindingResolutionException
     */
    public function scopeFilterByRequest(Builder $builder): void
    {
        /** @var Request $request */
        $request = Container::getInstance()->make(Request::class);

        $parameters = $request->array(ConfigHelper::requestFilterKey());

        $class = $this->eloquentFilterName();

        if (!class_exists($class)) {
            return;
        }

        /** @var EloquentFilter<TModel> $filter */
        $filter = app()->make($class);

        $filter($builder, $parameters);
    }

    private function eloquentFilterName(): string
    {
        return ConfigHelper::filterNamespace() . class_basename(static::class) . 'Filter';
    }
}
