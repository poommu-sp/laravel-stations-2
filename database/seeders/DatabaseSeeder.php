<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Schedule;
use App\Practice;
use App\Models\Sheet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Practice::factory(10)->create();
        Genre::factory(10)->create();
        Movie::factory(10)->create();
        Schedule::factory(10)->create();
        Sheet::factory(15)->create();
        
    }
}
