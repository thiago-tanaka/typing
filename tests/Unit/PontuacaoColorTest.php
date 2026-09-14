<?php

namespace Tests\Unit;

use App\Actions\GetPontuacoesGraficoAction;
use App\Actions\SetColorAction;
use App\Models\Digitacao;
use App\Models\Pontuacao;
use Tests\TestCase;

class PontuacaoColorTest extends TestCase
{
    public function test_color_depends_on_both_speed_and_accuracy()
    {
        $casos = [
            ['250', '98', Digitacao::COR_OTIMA],
            ['300', '97', Digitacao::COR_BOA],
            ['160', '97', Digitacao::COR_BOA],
            ['159', '99', Digitacao::COR_MEDIA],
            ['75', '96', Digitacao::COR_MEDIA],
            ['74', '100', Digitacao::COR_RUIM],
            ['500', '95', Digitacao::COR_RUIM],
        ];

        foreach ($casos as [$velocidade, $precisao, $cor]) {
            $html = (new SetColorAction)->setColor(new Pontuacao(['velocidade' => $velocidade, 'precisao' => $precisao]));

            $this->assertSame(
                '<span style="font-size:1.1em; color: '.$cor.'" class="p-1 mt-1 bg-pontuacao'.$cor.'">'.$velocidade.' / '.$precisao.'%</span>',
                $html,
                "$velocidade / $precisao%"
            );
        }
    }

    public function test_chart_lists_the_three_thresholds_from_best_to_worst()
    {
        $items = (new GetPontuacoesGraficoAction)(1);

        $this->assertCount(3, $items);
        $this->assertStringContainsString('250 / 98%', $items[0]);
        $this->assertStringContainsString(Digitacao::COR_OTIMA, $items[0]);
        $this->assertStringContainsString('160 / 97%', $items[1]);
        $this->assertStringContainsString(Digitacao::COR_BOA, $items[1]);
        $this->assertStringContainsString('75 / 96%', $items[2]);
        $this->assertStringContainsString(Digitacao::COR_MEDIA, $items[2]);
    }
}
