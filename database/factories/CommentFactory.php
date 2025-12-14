<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Mendefinisikan model dummy untuk tabel comments.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Sesuai dengan $table->unsignedBigInteger('news_id')
            // Otomatis membuat berita baru jika tidak ditentukan
            'news_id' => News::factory(), 

            // Sesuai dengan $table->unsignedBigInteger('user_id')
            // Otomatis membuat user baru jika tidak ditentukan
            'user_id' => User::factory(), 

            // PENTING: Sesuai dengan $table->text('body') di migrasi Anda
            'body' => fake()->sentence(), 

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}