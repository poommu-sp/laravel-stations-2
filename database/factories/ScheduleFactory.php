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
        // index for count current index of Movie
        static $index = 0;

        // get movie order by id from database 
        $movie = Movie::orderBy('id')->skip($index)->first();

        // if end of data then restart from first movie again
        if (!$movie) {
            $index = 0;
            $movie = Movie::orderBy('id')->skip($index)->first();
        }

        // plus index every round to update to next movie data
        $index++;


        return [
            'movie_id' =>  $movie->id,
            'start_time' => CarbonImmutable::now(),
            'end_time' => CarbonImmutable::now()->addHours(2),
            'screen_id' => $this->faker->numberBetween(1, 3)
        ];
    }
}
