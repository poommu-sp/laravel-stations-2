<?php

namespace Database\Factories;

use App\Models\Movie;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // get random exists genre_id from genre
        $movie = Movie::inRandomOrder()->first();

        return [
            'movie_id' =>  $movie->id,
            'start_time' => CarbonImmutable::now(),
            'end_time' => CarbonImmutable::now()->addHours(2),
        ];
    }
}
