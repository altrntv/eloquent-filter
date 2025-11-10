<?php

namespace Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Models\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'tag' => fake()->randomElement(['new', 'hot', 'vip', 'WOO', 'tests', 'prod', 'dev', 'local']),
            'published_at' => fake()->dateTimeBetween('-5 years'),
        ];
    }
}
