<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Post;
use App\Models\PostsComment;
use App\Models\Reel;
use App\Models\ReelsComment;
use App\Models\User;
use App\Models\users;
use App\Services\PixabayService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(20)->create();
        Post::factory(10)->create();
        News::factory(10)->create();
        Reel::factory(10)->create();

        $faker = Faker::create();
        $pixabayService = new PixabayService(); // Khởi tạo PixabayService
        $posts = Post::all();
        $reels = Reel::all();
        $users = User::all();
        foreach ($users as $user) {
            DB::table('user_follow')->insert([
                [
                    'user_id' => $user->id,
                    'user_following' => User::where('id', '!=', $user->id)->inRandomOrder()->first()->id,
                ],
            ]);
        }

        foreach ($posts as $post) {
            $mediaCount = rand(1, 5); // Random number of media items for each post

            for ($i = 0; $i < $mediaCount; $i++) {
                $mediaUrl = $pixabayService->getRandomMedia('nature'); // Gọi getRandomMedia

                // Insert the media record for the post
                if ($mediaUrl) {
                    DB::table('posts_media')->insert([
                        'post_id' => $post->id,
                        'media' => $mediaUrl,
                    ]);
                }
            }
            DB::table('posts_like')->insert([
                [
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ],
            ]);
            PostsComment::insert([
                [
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
                [
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
                [
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
            ]);
        }

        foreach ($reels as $reel) {
            DB::table('reels_like')->insert([
                [
                    'reels_id' => $reel->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ],
            ]);
            ReelsComment::insert([
                [
                    'reels_id' => $reel->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
                [
                    'reels_id' => $reel->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
                [
                    'reels_id' => $reel->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                    'comment' => $faker->text(100),
                ],
            ]);
        }
    }
}
