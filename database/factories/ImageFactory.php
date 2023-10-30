<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gallery_id' => Gallery::inRandomOrder()->select('id')->first(),
            'image' => fake()->imageUrl(640, 480, 'cats', true, 'Faker'),
            'order_number' => fake()->numberBetween(0, 10),

        ];
    }
}
