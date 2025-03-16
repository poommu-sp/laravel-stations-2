<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $currentSeatNumber = 0;

        $availableRows = ['a', 'b', 'c'];

        // cal seat column (1 to 5)
        $seatColumn = ($currentSeatNumber % 5) + 1;
        // cal loop for get index of availableRows (a to c)
        $rowIndex = intdiv($currentSeatNumber, 5) % count($availableRows);

        $currentSeatNumber++;

        return [
            'id' => $currentSeatNumber,
            'row' => $availableRows[$rowIndex], 
            'column' => $seatColumn, 
        ];
    }
}
