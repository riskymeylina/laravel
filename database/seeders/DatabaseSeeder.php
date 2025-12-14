<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Database\Factories\NewsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $users = User::factory(10)->create();
        $newsItems = News::factory(20)->create([
            'user_id' => fn() => $users->random()->id,
        ]);
        Comment::factory(50)->create([
            'news_id' => fn() => $newsItems->random()->id,
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
