<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Sesuai dengan $table->string('title')
            'title' => fake()->sentence(6), 
            
            // Sesuai dengan $table->text('content')
            'content' => fake()->paragraphs(4, true), 
            
            // Sesuai dengan $table->string('category')
            'category' => fake()->randomElement(['Food', 'Technology', 'Health', 'Lifestyle', 'Education']),
            
            // Sesuai dengan $table->string('image')->nullable()
            'image' => "news_images/default.jpg",
            
            // Sesuai dengan $table->unsignedBigInteger('user_id')
            // Otomatis membuat user baru jika tidak ditentukan saat pemanggilan
            'user_id' => User::factory(), 
            
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}