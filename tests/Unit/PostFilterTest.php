<?php

use Tests\Models\Post;

beforeEach(function () {
    Post::factory(30)
        ->state([
            'tag' => 'dev',
        ])
        ->create();

    Post::factory(10)
        ->state([
            'tag' => 'dev',
            'published_at' => '1999-10-20',
        ])
        ->create();

    Post::factory(2)
        ->state([
            'tag' => fake()->randomElement(['new', 'local']),
        ])
        ->create();
});

it('can be filtered by date', function () {
    expect(Post::filter(['published_at' => '1999-10-20'])->count())->toEqual(10)
        ->and(Post::query()->count())->toEqual(42);
});

it('can be filtered by array', function () {
    expect(Post::filter(['tag' => 'new,local'])->count())->toEqual(2);
});
