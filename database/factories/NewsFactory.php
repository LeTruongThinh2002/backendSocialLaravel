<?php

namespace Database\Factories;

use App\Services\PixabayService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pixabayService = new PixabayService();

        // Sử dụng phương thức getRandomMedia để lấy media ngẫu nhiên
        $mediaUrl = $pixabayService->getRandomMedia('nature');

        return [
            'description' => $this->faker->text(100),
            'media' => $mediaUrl,
            'user_id' => \App\Models\User::inRandomOrder()->first()->id,
        ];
    }
}
