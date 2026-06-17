<?php

namespace Altrntv\EloquentFilter\Filters\Concerns;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasParameters
{
    /**
     * @var array<string, mixed>
     */
    protected array $parameters = [];

    /**
     * @var array<string, string>
     */
    protected array $casts = [];

    public function bootParameters(): void
    {
        $this->initializeParameters();
    }

    private function initializeParameters(): void
    {
        foreach ($this->parameters as $key => $value) {
            $this->parameters[$key] = $this->castAttribute($key, $value);
        }
    }

    private function castAttribute(string $key, mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

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
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool)$value,

            'array' => $this->asArray($value),

            default => $value,
        };
    }

    /**
     * @return array<int, mixed>
     */
    private function asArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "array" cast expects a string or array, %s given.',
                    gettype($value)
                )
            );
        }

        return array_values(array_filter(array_map('trim', explode(ConfigHelper::arrayValueSeparator(), $value))));
    }
}
