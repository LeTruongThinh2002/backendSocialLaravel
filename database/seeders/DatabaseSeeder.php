<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\users;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'),
            'date_of_birth' => '2015-10-10',
            'country' => 'US',
        ]);
    }
}
