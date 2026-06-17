<?php

namespace Altrntv\EloquentFilter\Sorts;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Altrntv\EloquentFilter\Contracts\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TModel of Model
 *
 * @implements Sort<TModel>
 */
abstract class EloquentSort implements Sort
{
    protected const string ASC_DIRECTION = 'asc';

    protected const string DESC_DIRECTION = 'desc';

    /** @var Builder<TModel> */
    protected Builder $builder;

    /**
     * @var string[]
     */
    protected array $columns = [];

    protected string $parameters;

    /**
     * @param Builder<TModel> $builder
     */
    public function __invoke(Builder $builder, string $parameters): void
    {
        $this->builder = $builder;
        $this->parameters = $parameters;

        $this->initializeParameters();

        foreach ($this->columns as $column => $direction) {
            $method = Str::camel($column);

            if (method_exists($this, $method)) {
                $this->{$method}($direction);
            }
        }
    }

    protected function initializeParameters(): void
    {
        $this->columns = Str::of($this->parameters)
            ->explode(ConfigHelper::sortValueSeparator())
            ->filter(function (string $value): bool {
                $trimmed = trim($value);

                if (Str::startsWith($value, '-')) {
                    return Str::length($trimmed) > 1;
                }

                return Str::length($trimmed) > 0;
            })
            ->mapWithKeys(function (string $value): array {
                if (Str::startsWith($value, '-')) {
                    $value = Str::replaceFirst('-', '', $value);
                    $direction = self::DESC_DIRECTION;
                } else {
                    $direction = self::ASC_DIRECTION;
                }

                return [$value => $direction];
            })
            ->all();
    }

    protected function reverse(string $direction): string
    {
        return $direction === self::ASC_DIRECTION
            ? self::DESC_DIRECTION
            : self::ASC_DIRECTION;
    }
}
