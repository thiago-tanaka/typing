<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Pontuacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PontuacaoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pontuacao::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'velocidade' => '100',
            'precisao' => '97',
        ];
    }
}
