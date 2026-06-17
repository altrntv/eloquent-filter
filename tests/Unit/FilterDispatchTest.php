<?php

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Tests\Models\User;

describe('Filter parameter dispatch', function () {
    beforeEach(function () {
        User::factory(5)->state(['role' => 1])->create();
        User::factory(3)->state(['role' => 2])->create();
    });

    it('skips null parameter values', function () {
        expect(User::filter(['role' => null])->count())->toBe(8);
    });

    it('skips unknown parameter keys without error', function () {
        expect(User::filter(['unknownKey' => 'value'])->count())->toBe(8);
    });

    it('returns all records when parameters array is empty', function () {
        expect(User::filter([])->count())->toBe(8);
    });

    it('handles mix of null and valid parameters', function () {
        expect(User::filter(['nonExistent' => null, 'role' => '2'])->count())->toBe(3);
    });

    it('blocks calling bootParameters via user-supplied key', function () {
        expect(fn () => User::filter(['boot_parameters' => 'anything']))->not->toThrow(Throwable::class);
        expect(User::filter(['boot_parameters' => 'anything'])->count())->toBe(8);
    });

    it('blocks calling bootJoinParameters via user-supplied key', function () {
        expect(fn () => User::filter(['boot_join_parameters' => 'anything']))->not->toThrow(Throwable::class);
        expect(User::filter(['boot_join_parameters' => 'anything'])->count())->toBe(8);
    });

    it('applies valid filters while blocked infrastructure keys are ignored', function () {
        $count = User::filter([
            'boot_parameters'      => 'anything',
            'boot_join_parameters' => 'anything',
            'role'                 => '1',
        ])->count();

        expect($count)->toBe(5);
    });

    it('calls filter method only on the concrete class, not inherited infrastructure', function () {
        $callCount = 0;

        $filter = new class ($callCount) extends EloquentFilter {
            public function __construct(private int &$counter) {}

            public function role(mixed $value): void
            {
                $this->counter++;
                $this->builder->where('role', $value);
            }
        };

        $filter(User::query(), ['boot_parameters' => 'x', 'role' => 1]);

        expect($callCount)->toBe(1);
    });
});
