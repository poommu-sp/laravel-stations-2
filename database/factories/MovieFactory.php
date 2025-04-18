<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // get random exists genre_id from genre
        $genre = Genre::inRandomOrder()->first();
        
        return [
            'title' => $this->faker->unique()->word,
            'image_url' => 'https://picsum.photos/640/480?random=' . rand(1, 1000),
            'published_year' => $this->faker->year,
            'description' => $this->faker->realText(20),
            'is_showing' => $this->faker->boolean,
            'genre_id' => $genre->id
        ];
    }
}
