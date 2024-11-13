<?php
namespace Database\Factories;

use App\Services\PixabayService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reel>
 */
class ReelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pixabayService = new PixabayService();
        $videoData = $pixabayService->searchVideos('nature', 100);

        // Kiểm tra xem có video nào được trả về không và chọn ngẫu nhiên một video
        $videoUrl = null;
        if (!empty($videoData['hits'])) {
            $randomVideo = $this->faker->randomElement($videoData['hits']);
            $videoUrl = $randomVideo['videos']['medium']['url'];
        }

        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()->id,
            'description' => $this->faker->text(100),
            'media' => $videoUrl,
        ];
    }
}
