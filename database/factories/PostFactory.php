<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Đảm bảo user_id luôn là một số nguyên hợp lệ
        return [
            'description' => json_encode(
                [
                    'html' => $this->faker->randomHtml(2, 3),
                    'json' => [
                        $this->faker->text(10)
                    ]
                ]
            ),
            'user_id' => \App\Models\User::inRandomOrder()->first()->id, // Kiểm tra xem user có tồn tại không
        ];
    }
}
