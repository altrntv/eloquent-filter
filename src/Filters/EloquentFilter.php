<?php

namespace Altrntv\EloquentFilter\Filters;

use Altrntv\EloquentFilter\Contracts\Filter;
use Altrntv\EloquentFilter\Filters\Concerns\HasJoinParameters;
use Altrntv\EloquentFilter\Filters\Concerns\HasParameters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionMethod;

/**
 * @template TModel of Model
 *
 * @implements Filter<TModel>
 */
abstract class EloquentFilter implements Filter
{
    use HasParameters;
    use HasJoinParameters;

    /** @var Builder<TModel> */
    protected Builder $builder;

    public function __invoke(Builder $builder, array $parameters = []): void
    {
        $this->builder = $builder;
        $this->parameters = $parameters;

        $this->bootParameters();
        $this->bootJoinParameters();

        foreach ($this->parameters as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $method = Str::camel($key);

            if (!method_exists($this, $method)) {
                continue;
            }

            // Запрещаем вызов инфраструктурных методов через пользовательские параметры
            $declaringClass = (new ReflectionMethod($this, $method))->getDeclaringClass()->getName();
            if (in_array($declaringClass, [self::class, HasParameters::class, HasJoinParameters::class], true)) {
                continue;
            }

            if (array_key_exists($key, $this->joinParameters)) {
                $this->{$method}(...$value);

                continue;
            }

            $this->{$method}($value);
        }
    }
}
