<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'main_image' => fake()->imageUrl(640, 480, 'cats', true, 'Faker'),
            'name' => fake()->name(),
            'user_id'=> User::inRandomOrder()->select('id')->first(),
         ];
    }
}
