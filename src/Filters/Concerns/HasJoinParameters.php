<?php

namespace Altrntv\EloquentFilter\Filters\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasJoinParameters
{
    /**
     * @var array<string, string[]>
     */
    protected array $joinParameters = [];

    public function bootJoinParameters(): void
    {
        $this->initializeJoinParameters();
    }

    private function initializeJoinParameters(): void
    {
        foreach ($this->joinParameters as $key => $value) {
            $values = Arr::only($this->parameters, $value);

            if (empty($values)) {
                continue;
            }

            // Если пришли не все поля группы — удаляем частичные параметры
            // и пропускаем группу, чтобы не вызвать метод с неполными аргументами
            Arr::forget($this->parameters, array_keys($values));

            if (count($values) !== count($value)) {

                continue;
            }

            $this->parameters[$key] = Arr::mapWithKeys($values, static function (mixed $value, string $key): array {
                return [Str::camel($key) => $value];
            });
        }
    }
}
