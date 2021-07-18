<?php


namespace App\Actions;


use App\Models\Digitacao;
use App\Models\Pontuacao;

class GetPontuacoesAction
{
    public function __invoke($unidade): array
    {
        $pontuacoes = [1 => '',2 => '',3 => '',4 => '',5 => '',];

        auth()->user()->pontuacoes()->whereHas('lesson', function($query) use ($unidade){
            $query->whereHas('unit', function($query) use($unidade){
                $query->where('name', $unidade);
            });
        })->each(function ($pontuacao) use(&$pontuacoes){
            $pontuacoes[$pontuacao->lesson->name] = $this->setColor($pontuacao);
        });

        return $pontuacoes;
    }

    public function setColor(Pontuacao $pontuacao): string
    {
        if ($pontuacao->velocidade >= Digitacao::VELOCIDADE_OTIMA && $pontuacao->precisao >= Digitacao::PRECISAO_OTIMA) {
            $color = 'purple';
        } elseif ($pontuacao->velocidade >= Digitacao::VELOCIDADE_BOA && $pontuacao->precisao >= Digitacao::PRECISAO_BOA) {
            $color = 'success';
        } elseif ($pontuacao->velocidade >= Digitacao::VELOCIDADE_MEDIA && $pontuacao->precisao >= Digitacao::PRECISAO_MEDIA) {
            $color = 'amarelo';
        } else {
            $color = 'danger';
        }

        return '<span style="font-size:1.1em" class="p-1 mt-1 bg-pontuacao text-' . $color . '">' . $pontuacao . '</span>';
    }
}
