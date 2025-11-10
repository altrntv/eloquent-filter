<?php

use Tests\Models\User;

beforeEach(function () {
    User::factory()
        ->state([
            'name' => 'AAA last sort name',
        ])
        ->create();

    User::factory()
        ->state([
            'name' => 'HHH' . fake()->word(),
            'age' => 12,
        ])
        ->create();

    User::factory(25)
        ->state([
            'name' => 'HHH' . fake()->word(),
        ])
        ->create();

    User::factory()
        ->state([
            'name' => 'ZZZ last sort name',
        ])
        ->create();

    User::factory()
        ->state([
            'name' => 'HHH' . fake()->word(),
            'age' => 101,
        ])
        ->create();
});

it('is first when sorting by name', function () {
    expect(User::sort('name')->first())->name->toEqual('AAA last sort name')
        ->and(User::sort('-name')->first())->name->toEqual('ZZZ last sort name');
});

it('is first when sorting by age', function () {
    expect(User::sort('age')->first())->age->toEqual(12)
        ->and(User::sort('-age')->first())->age->toEqual(101);
});
