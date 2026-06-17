<?php

use Altrntv\EloquentFilter\Sorts\EloquentSort;
use Tests\Models\User;

describe('Sort parameter parsing', function () {
    beforeEach(function () {
        User::factory()->state(['name' => 'Alice', 'age' => 30])->create();
        User::factory()->state(['name' => 'Alice', 'age' => 20])->create();
        User::factory()->state(['name' => 'Bob',   'age' => 25])->create();
        User::factory()->state(['name' => 'Charlie', 'age' => 40])->create();
    });

    describe('single column', function () {
        it('sorts ascending when no dash prefix is given', function () {
            expect(User::sort('name')->first()->name)->toBe('Alice');
        });

        it('sorts descending when a dash prefix is given', function () {
            expect(User::sort('-name')->first()->name)->toBe('Charlie');
        });
    });

    describe('multiple columns', function () {
        it('applies all columns in declaration order', function () {
            // name ASC then age ASC: two Alices → the younger one (20) comes first
            $users = User::sort('name,age')->get();

            expect($users->first()->name)->toBe('Alice')
                ->and($users->first()->age)->toBe(20);
        });

        it('supports mixed directions across columns', function () {
            // name ASC, age DESC: two Alices → the older one (30) comes first
            $users = User::sort('name,-age')->get();

            expect($users->first()->name)->toBe('Alice')
                ->and($users->first()->age)->toBe(30);
        });
    });

    describe('edge cases', function () {
        it('silently skips a standalone dash without error', function () {
            expect(fn () => User::sort('-'))->not->toThrow(Throwable::class);
            expect(User::sort('-')->count())->toBe(4);
        });

        it('handles an empty sort string without error', function () {
            expect(fn () => User::sort(''))->not->toThrow(Throwable::class);
            expect(User::sort('')->count())->toBe(4);
        });

        it('ignores whitespace-only entries between separators', function () {
            expect(fn () => User::sort(' , , '))->not->toThrow(Throwable::class);
            expect(User::sort(' , , ')->count())->toBe(4);
        });

        it('silently skips unknown column names that have no sort method', function () {
            expect(fn () => User::sort('unknownColumn'))->not->toThrow(Throwable::class);
            expect(User::sort('unknownColumn')->count())->toBe(4);
        });

        it('handles column names with surrounding whitespace', function () {
            // ' name ' should still resolve to the name() method
            expect(User::sort(' name ')->first()->name)->toBe('Alice');
        });
    });

    describe('reverse() helper', function () {
        it('returns desc when given asc', function () {
            $sort = new class extends EloquentSort {
                public function expose(string $direction): string
                {
                    return $this->reverse($direction);
                }
            };

            expect($sort->expose('asc'))->toBe('desc');
        });

        it('returns asc when given desc', function () {
            $sort = new class extends EloquentSort {
                public function expose(string $direction): string
                {
                    return $this->reverse($direction);
                }
            };

            expect($sort->expose('desc'))->toBe('asc');
        });
    });
});
