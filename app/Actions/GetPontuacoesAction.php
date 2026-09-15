<?php

namespace App\Actions;

use App\Models\Digitacao;

class GetPontuacoesAction
{
    /**
     * The logged-in user's score for each lesson of a unit, keyed by lesson
     * name (null when the lesson has no score yet).
     */
    public function __invoke($unidade): array
    {
        $pontuacoes = [1 => null, 2 => null, 3 => null, 4 => null, 5 => null];

        auth()->user()->pontuacoes()->whereHas('lesson', function ($query) use ($unidade) {
            $query->whereHas('unit', function ($query) use ($unidade) {
                $query->where('name', $unidade);
            });
        })->with('lesson')->each(function ($pontuacao) use (&$pontuacoes) {
            $pontuacoes[(int) $pontuacao->lesson->name] = [
                'velocidade' => (int) $pontuacao->velocidade,
                'precisao' => (int) $pontuacao->precisao,
                'nivel' => Digitacao::nivel($pontuacao->velocidade, $pontuacao->precisao),
            ];
        });

        return $pontuacoes;
    }
}
