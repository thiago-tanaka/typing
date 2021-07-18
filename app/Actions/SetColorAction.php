<?php


namespace App\Actions;


use App\Models\Digitacao;
use App\Models\Pontuacao;

class SetColorAction
{

    public function setColor(Pontuacao $pontuacao): string
    {
        if ($pontuacao->velocidade >= Digitacao::VELOCIDADE_OTIMA && $pontuacao->precisao >= Digitacao::PRECISAO_OTIMA) {
            $color = Digitacao::COR_OTIMA;
        } elseif ($pontuacao->velocidade >= Digitacao::VELOCIDADE_BOA && $pontuacao->precisao >= Digitacao::PRECISAO_BOA) {
            $color = Digitacao::COR_BOA;
        } elseif ($pontuacao->velocidade >= Digitacao::VELOCIDADE_MEDIA && $pontuacao->precisao >= Digitacao::PRECISAO_MEDIA) {
            $color = Digitacao::COR_MEDIA;
        } else {
            $color = Digitacao::COR_RUIM;
        }

        return '<span style="font-size:1.1em; color: ' . $color . '" class="p-1 mt-1 bg-pontuacao' . $color . '">' . $pontuacao . '</span>';
    }
}
