<?php

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Tests\Models\User;

describe('Filter parameter casts', function () {

    describe('array cast', function () {
        it('converts a comma-separated string to an array', function () {
            $filter = new class extends EloquentFilter {
                public array $captured = [];

                protected array $casts = ['tags' => 'array'];

                public function tags(array $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['tags' => 'php,laravel,vue']);

            expect($filter->captured)->toBe(['php', 'laravel', 'vue']);
        });

        it('trims whitespace from array elements', function () {
            $filter = new class extends EloquentFilter {
                public array $captured = [];

                protected array $casts = ['tags' => 'array'];

                public function tags(array $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['tags' => ' php , laravel , vue ']);

            expect($filter->captured)->toBe(['php', 'laravel', 'vue']);
        });

        it('passes an already-array value through unchanged', function () {
            $filter = new class extends EloquentFilter {
                public array $captured = [];

                protected array $casts = ['tags' => 'array'];

                public function tags(array $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['tags' => ['php', 'laravel']]);

            expect($filter->captured)->toBe(['php', 'laravel']);
        });

        it('filters out empty elements after split', function () {
            $filter = new class extends EloquentFilter {
                public array $captured = [];

                protected array $casts = ['tags' => 'array'];

                public function tags(array $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['tags' => 'php,,laravel,']);

            expect($filter->captured)->toBe(['php', 'laravel']);
        });

        it('throws InvalidArgumentException when a non-string non-array is given', function () {
            $filter = new class extends EloquentFilter {
                protected array $casts = ['ids' => 'array'];

                public function ids(array $value): void
                {
                }
            };

            expect(fn() => $filter(User::query(), ['ids' => 123]))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('integer cast', function () {
        it('converts a numeric string to int before dispatching', function () {
            $filter = new class extends EloquentFilter {
                public int $captured = 0;

                protected array $casts = ['age' => 'integer'];

                public function age(int $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['age' => '42']);

            expect($filter->captured)->toBe(42)->toBeInt();
        });

        it('truncates float strings to int', function () {
            $filter = new class extends EloquentFilter {
                public int $captured = 0;

                protected array $casts = ['age' => 'int'];

                public function age(int $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['age' => '18.9']);

            expect($filter->captured)->toBe(18);
        });
    });

    describe('string cast', function () {
        it('converts an integer to string', function () {
            $filter = new class extends EloquentFilter {
                public string $captured = '';

                protected array $casts = ['name' => 'string'];

                public function name(string $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['name' => 42]);

            expect($filter->captured)->toBe('42')->toBeString();
        });
    });

    describe('boolean cast', function () {
        it('casts truthy string values to true', function () {
            $filter = new class extends EloquentFilter {
                public ?bool $captured = null;

                protected array $casts = ['active' => 'boolean'];

                public function active(bool $value): void
                {
                    $this->captured = $value;
                }
            };

            foreach (['1', 'true', 'yes', 'on'] as $truthy) {
                $filter->captured = null;
                $filter(User::query(), ['active' => $truthy]);
                expect($filter->captured)->toBeTrue("Expected '{$truthy}' to cast to true");
            }
        });

        it('casts falsy string values to false', function () {
            $filter = new class extends EloquentFilter {
                public ?bool $captured = null;

                protected array $casts = ['active' => 'bool'];

                public function active(bool $value): void
                {
                    $this->captured = $value;
                }
            };

            foreach (['0', 'false', 'no', 'off'] as $falsy) {
                $filter->captured = null;
                $filter(User::query(), ['active' => $falsy]);
                expect($filter->captured)->toBeFalse("Expected '{$falsy}' to cast to false");
            }
        });
    });

    describe('unknown cast type', function () {
        it('passes the value through unchanged for unrecognised cast types', function () {
            $filter = new class extends EloquentFilter {
                public mixed $captured = null;

                protected array $casts = ['meta' => 'custom_type'];

                public function meta(mixed $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['meta' => 'raw_value']);

            expect($filter->captured)->toBe('raw_value');
        });
    });

    describe('no cast defined', function () {
        it('passes the raw value when no cast is configured for the key', function () {
            $filter = new class extends EloquentFilter {
                public mixed $captured = null;

                public function role(mixed $value): void
                {
                    $this->captured = $value;
                }
            };

            $filter(User::query(), ['role' => '5']);

            expect($filter->captured)->toBe('5')->toBeString();
        });
    });
});
