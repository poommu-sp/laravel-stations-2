<?php

namespace Database\Seeders;

use App\Models\Sheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SheetTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            ['id' => 1, 'column' => 1, 'row' => 'A'],
            ['id' => 2, 'column' => 2, 'row' => 'A'],
            ['id' => 3, 'column' => 3, 'row' => 'A'],
            ['id' => 4, 'column' => 4, 'row' => 'A'],
            ['id' => 5, 'column' => 5, 'row' => 'A'],
            ['id' => 6, 'column' => 1, 'row' => 'B'],
            ['id' => 7, 'column' => 2, 'row' => 'B'],
            ['id' => 8, 'column' => 3, 'row' => 'B'],
            ['id' => 9, 'column' => 4, 'row' => 'B'],
            ['id' => 10, 'column' => 5, 'row' => 'B'],
            ['id' => 11, 'column' => 1, 'row' => 'C'],
            ['id' => 12, 'column' => 2, 'row' => 'C'],
            ['id' => 13, 'column' => 3, 'row' => 'C'],
            ['id' => 14, 'column' => 4, 'row' => 'C'],
            ['id' => 15, 'column' => 5, 'row' => 'C'],
        ];

        foreach ($data as $sheet) {
            Sheet::create($sheet);
        }
  }
}