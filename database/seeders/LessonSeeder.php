<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('licoes') as $index => $unit) {
            $unitModel = Unit::create([
                'name' => $index
            ]);
            foreach ($unit as $key => $lesson) {
                $unitModel->lessons()->create([
                    'name' => $key,
                    'text1' => $lesson[1],
                    'text2' => $lesson[2],
                    'text3' => $lesson[3],
                    'text4' => $lesson[4],
                ]);

            }
        }
    }
}
