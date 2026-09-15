<?php

namespace App\Actions;

use App\Models\Digitacao;
use App\Models\Lesson;
use App\Models\Pontuacao;

class PontuacaoNovaEMaiorAction
{
    /**
     * Whether a new result should replace the user's score for the lesson:
     * a higher level wins and, within the same level, the higher net speed.
     */
    public function __invoke(Lesson $lesson, $velocidade, $precisao): bool
    {
        $pontuacao = Pontuacao::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first();

        if (! $pontuacao) {
            return true;
        }

        return Digitacao::compararResultados($velocidade, $precisao, $pontuacao->velocidade, $pontuacao->precisao) >= 0;
    }
}
