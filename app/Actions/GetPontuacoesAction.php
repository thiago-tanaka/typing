<?php

namespace App\Actions;

use App\Models\Pontuacao;

class GetPontuacoesAction
{

    private $setColorAction;

    public function __construct()
    {
        $this->setColorAction = new SetColorAction();
    }

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
        return $this->setColorAction->setColor($pontuacao);
    }
}
