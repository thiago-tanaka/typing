<?php


namespace App\Actions;


use App\Models\Digitacao;
use App\Models\Pontuacao;

class GetPontuacoesGraficoAction
{
    private $setColorAction;

    public function __construct()
    {
        $this->setColorAction = new SetColorAction();
    }

    public function __invoke($unidade): array
    {
        $items = [];

        $pontuacao = new Pontuacao([
            'velocidade' => Digitacao::VELOCIDADE_OTIMA,
            'precisao' => Digitacao::PRECISAO_OTIMA,
        ]);
        $items[] = $this->setColor($pontuacao);

        $pontuacao = new Pontuacao([
            'velocidade' => Digitacao::VELOCIDADE_BOA,
            'precisao' => Digitacao::PRECISAO_BOA,
        ]);
        $items[] = $this->setColor($pontuacao);

        $pontuacao = new Pontuacao([
            'velocidade' => Digitacao::VELOCIDADE_MEDIA,
            'precisao' => Digitacao::PRECISAO_MEDIA,
        ]);
        $items[] = $this->setColor($pontuacao);
        return $items;
    }

    public function setColor(Pontuacao $pontuacao): string
    {
        return $this->setColorAction->setColor($pontuacao);
    }
}
