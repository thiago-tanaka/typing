<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'unit_id' => Unit::factory(),
            'name' => '1',
            'text1' => 'asdfg lkjh asdfg lkjh asdfg lkjh',
            'text2' => 'we oi we oi we oi we oi we oi',
            'text3' => 'rt uy rt uy rt uy rt uy rt uy',
            'text4' => 'qwert poiuy qwert poiuy qwert',
        ];
    }
}
