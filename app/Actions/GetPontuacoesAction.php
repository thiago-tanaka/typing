<?php


namespace App\Actions;


use App\Models\Digitacao;
use Exception;

class GetPontuacoesAction
{
    public function __invoke($unidade): array
    {
        $pontuacoes = [];

        for ($i = 1; $i <= 5; $i++) {
            $pontuacoes[$i] = $this->setColor(auth()->user()["licao_{$unidade}_{$i}"]);
        }

        return $pontuacoes;
    }

    public function setColor(?string $licao): string
    {
        if (!(new LicaoIsValid)($licao)) {
            return '';
        }

        $velocidade = (new GetVelocidadeAction)($licao);
        $precisao = (new GetPrecisaoAction)($licao);

        if ($velocidade >= Digitacao::VELOCIDADE_OTIMA && $precisao >= Digitacao::PRECISAO_OTIMA) {
            $color = 'purple';
        } elseif ($velocidade >= Digitacao::VELOCIDADE_BOA && $precisao >= Digitacao::PRECISAO_BOA) {
            $color = 'success';
        } elseif ($velocidade >= Digitacao::VELOCIDADE_MEDIA && $precisao >= Digitacao::PRECISAO_MEDIA) {
            $color = 'amarelo';
        } else {
            $color = 'danger';
        }

        return '<span style="font-size:1.1em" class="p-1 mt-1 bg-pontuacao text-' . $color . '">' . $licao . '</span>';
    }
}
