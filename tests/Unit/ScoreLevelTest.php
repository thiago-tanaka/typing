<?php

namespace Tests\Unit;

use App\Models\Digitacao;
use PHPUnit\Framework\TestCase;

class ScoreLevelTest extends TestCase
{
    public function test_level_depends_on_both_speed_and_accuracy()
    {
        $casos = [
            ['250', '98', 'excellent'],
            ['300', '97', 'good'],
            ['160', '97', 'good'],
            ['159', '99', 'average'],
            ['75', '96', 'average'],
            ['74', '100', 'practice'],
            ['500', '95', 'practice'],
        ];

        foreach ($casos as [$velocidade, $precisao, $nivel]) {
            $this->assertSame($nivel, Digitacao::nivel($velocidade, $precisao), "$velocidade / $precisao%");
        }
    }

    public function test_levels_are_listed_from_best_to_worst()
    {
        $this->assertSame([
            ['nivel' => 'excellent', 'velocidade' => 250, 'precisao' => 98],
            ['nivel' => 'good', 'velocidade' => 160, 'precisao' => 97],
            ['nivel' => 'average', 'velocidade' => 75, 'precisao' => 96],
        ], Digitacao::niveis());
    }
}
