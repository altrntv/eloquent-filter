<?php

use Tests\Models\Post;

beforeEach(function () {
    $this->past = now()->subYears(random_int(6, 10));
    $this->future = now()->addDays(random_int(1, 30));

    Post::factory(25)
        ->create();

    Post::factory(2)
        ->sequence(
            [
                'published_at' => $this->past,
            ],
            [
                'published_at' => $this->future,
            ],
        )
        ->create();
});

it('is first when sorting by published at', function () {
    expect(Post::sort('published_at')->first())->published_at->toEqual($this->past->toDateTimeString())
        ->and(Post::sort('-published_at')->first())->published_at->toEqual($this->future->toDateTimeString());
});
