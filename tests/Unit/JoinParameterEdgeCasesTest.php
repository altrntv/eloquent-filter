<?php

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Tests\Models\User;

describe('Join parameter edge cases', function () {
    beforeEach(function () {
        User::factory(10)->state(['age' => fake()->numberBetween(20, 30)])->create();
        User::factory(5)->state(['age'  => fake()->numberBetween(60, 65)])->create();
    });

    it('skips the join group without error when only one field of the pair is present', function () {
        // Only age_from is present — group should be silently skipped, all users returned
        expect(fn () => User::filter(['age_from' => 20]))->not->toThrow(Throwable::class);
        expect(User::filter(['age_from' => 20])->count())->toBe(15);
    });

    it('skips the join group without error when only the second field is present', function () {
        expect(fn () => User::filter(['age_to' => 30]))->not->toThrow(Throwable::class);
        expect(User::filter(['age_to' => 30])->count())->toBe(15);
    });

    it('applies the join filter when all fields of the group are present', function () {
        expect(User::filter(['age_from' => 20, 'age_to' => 30])->count())->toBe(10);
    });

    it('returns all records when no join fields are present', function () {
        expect(User::filter([])->count())->toBe(15);
    });

    it('does not dispatch the individual join keys as separate filter methods', function () {
        $callCount = 0;

        $filter = new class ($callCount) extends EloquentFilter {
            protected array $joinParameters = [
                'range' => ['from', 'to'],
            ];

            public function __construct(private int &$counter) {}

            public function range(mixed $from, mixed $to): void
            {
                $this->counter++;
            }

            // These should NOT be called directly
            public function from(mixed $value): void
            {
                $this->counter += 100;
            }

            public function to(mixed $value): void
            {
                $this->counter += 100;
            }
        };

        // Only one field present — group should be skipped entirely
        $filter(User::query(), ['from' => '10']);

        expect($callCount)->toBe(0);
    });

    it('calls the join method exactly once when both fields are present', function () {
        $callCount = 0;

        $filter = new class ($callCount) extends EloquentFilter {
            protected array $joinParameters = [
                'range' => ['from', 'to'],
            ];

            public function __construct(private int &$counter) {}

            public function range(mixed $from, mixed $to): void
            {
                $this->counter++;
            }
        };

        $filter(User::query(), ['from' => '10', 'to' => '20']);

        expect($callCount)->toBe(1);
    });
});
