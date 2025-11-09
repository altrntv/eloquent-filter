<?php

namespace Altrntv\EloquentFilter\Filters;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TModel of Model
 */
abstract class EloquentSort
{
    private const string ASC_DIRECTION = 'asc';

    private const string DESC_DIRECTION = 'desc';

    /** @var Builder<TModel> */
    protected Builder $builder;

    /**
     * @var string[]
     */
    protected array $columns = [];

    public function __construct(protected string $parameters)
    {
        $this->initializeParameters();
    }

    /**
     * @param Builder<TModel> $builder
     *
     * @return Builder<TModel>
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->columns as $column => $direction) {
            $method = Str::camel($column);

            if (method_exists($this, $method)) {
                $this->{$method}($direction);
            }
        }

        return $this->builder;
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
