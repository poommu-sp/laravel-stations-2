<?php

namespace Database\Seeders;

use App\Models\Screen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScreenTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            ['id' => 1, 'name' => 'スクリーン1'],
            ['id' => 2, 'name' => 'スクリーン2'],
            ['id' => 3, 'name' => 'スクリーン3'],
        ];

        foreach ($data as $screen) {
            Screen::create($screen);
        }
  }
}