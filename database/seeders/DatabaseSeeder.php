<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Schedule;
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
        $this->call(ScreenTableSeeder::class);
        $this->call(SheetTableSeeder::class);
        Schedule::factory(10)->create();
        
    }
}
