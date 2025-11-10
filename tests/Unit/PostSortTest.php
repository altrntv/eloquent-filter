<?php

use Tests\Models\Post;

beforeEach(function () {
    Post::factory()
        ->state([
            'published_at' => '1999-11-10',
        ])
        ->create();

    Post::factory(25)->create();

    Post::factory()
        ->state([
            'published_at' => '2025-11-10',
        ])
        ->create();
});

it('is first when sorting by published at', function () {
    expect(Post::sort('published_at')->first())->published_at->toEqual('1999-11-10')
        ->and(Post::sort('-published_at')->first())->published_at->toEqual('2025-11-10');
});
