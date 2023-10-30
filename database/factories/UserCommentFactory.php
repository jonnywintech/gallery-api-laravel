<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserComment>
 */
class UserCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=> User::inRandomOrder()->select('id')->first(),
            'gallery_id'=> Gallery::inRandomOrder()->select('id')->first(),
            'comment_id'=> Comment::inRandomOrder()->select('id')->first(),
        ];
    }
}
