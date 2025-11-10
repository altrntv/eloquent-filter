<?php

use Tests\Models\User;

beforeEach(function () {
    User::factory(15)
        ->state([
            'role' => fake()->numberBetween(1, 2),
            'age' => 10,
        ])
        ->create();

    User::factory(10)
        ->state([
            'role' => fake()->numberBetween(3, 4),
            'age' => 10,
        ])
        ->create();

    User::factory(30)
        ->state([
            'role' => 5,
            'age' => fake()->numberBetween(20, 30),
        ])
        ->create();

    User::factory(5)
        ->state([
            'role' => 6,
            'age' => fake()->numberBetween(60, 65),
        ])
        ->create();
});

it('can be filtered by array', function () {
    expect(User::filter(['role' => '1,2'])->count())->toEqual(15)
        ->and(User::filter(['role' => '3,4'])->count())->toEqual(10);
});

it('can be filtered by join parameters', function () {
    expect(User::filter(['age_from' => 20, 'age_to' => 30])->count())->toEqual(30)
        ->and(User::filter(['age_from' => 60, 'age_to' => 65])->count())->toEqual(5);
});
