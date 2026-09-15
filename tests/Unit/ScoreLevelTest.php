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

    public function test_results_compare_by_level_first_and_then_by_net_speed()
    {
        // A higher level wins, even against a faster result with poor accuracy.
        $this->assertGreaterThan(0, Digitacao::compararResultados(250, 98, 1000, 90));
        $this->assertGreaterThan(0, Digitacao::compararResultados(300, 99, 75, 100));

        // Within the same level, the higher net speed wins.
        $this->assertGreaterThan(0, Digitacao::compararResultados(200, 96, 120, 97));
        $this->assertLessThan(0, Digitacao::compararResultados(150, 97, 200, 96));

        // Identical results are equivalent, whether they come as numbers or strings.
        $this->assertSame(0, Digitacao::compararResultados(120, 97, '120', '97'));
    }
}
