<?php

namespace Altrntv\EloquentFilter\Filters;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @template TModel of Model
 */
abstract class EloquentFilter
{
    /** @var Builder<TModel> */
    protected Builder $builder;

    /**
     * @var array<string, string>
     */
    protected array $casts = [];

    /**
     * @var array<string, string[]>
     */
    protected array $joinParameters = [];

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(protected array $parameters = [])
    {
        $this->initializeParameters();
        $this->initializeJoinParameters();
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @return Builder<TModel>
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->parameters as $key => $value) {
            $method = Str::camel($key);

            if (array_key_exists($key, $this->joinParameters)) {
                $this->{$method}(...$value);

                continue;
            }

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this->builder;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultParameters(): array
    {
        return [];
    }

    protected function initializeParameters(): void
    {
        foreach ($this->parameters as $key => $value) {
            $this->parameters[$key] = $this->castAttribute($key, $value);
        }
    }

    protected function initializeJoinParameters(): void
    {
        foreach ($this->joinParameters as $key => $value) {
            $values = Arr::only($this->parameters, $value);

            if (empty($values)) {
                continue;
            }

            Arr::forget($this->parameters, array_keys($values));

            $this->parameters[$key] = Arr::mapWithKeys($values, static function (mixed $value, string $key) {
                return [Str::camel($key) => $value];
            });
        }
    }

    protected function castAttribute(string $key, mixed $value): mixed
    {
        /** @var string|null $castType */
        $castType = Arr::get($this->casts, $key);

        if (is_null($castType)) {
            return $value;
        }

        return match ($castType) {
            'int',
            'integer' => (int)$value,

            'string' => (string)$value,

            'bool',
            'boolean' => (bool)$value,

            'array' => $this->asArray($value),

            default => $value,
        };
    }

    /**
     * @return array<int, mixed>
     */
    protected function asArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            throw throw new InvalidArgumentException(
                sprintf(
                    'The "array" cast expects a string or array, %s given.',
                    gettype($value)
                )
            );
        }

        return array_filter(explode(ConfigHelper::arrayValueSeparator(), $value));
    }
}
