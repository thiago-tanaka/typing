<?php

namespace App\Actions;

use App\Models\Lesson;
use App\Models\Pontuacao;

class PontuacaoNovaEMaiorAction
{
    public function __invoke(Lesson $lesson, $velocidade, $precisao): bool
    {
        $pontuacao = Pontuacao::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first();

        if(!$pontuacao){
            return true;
        }

        $current_precisao = $pontuacao->precisao;
        $current_valocidade = $pontuacao->velocidade;

        $new_precisao = $precisao;
        $new_velocidade = $velocidade;

        return $new_precisao >= $current_precisao &&
            $new_velocidade >= $current_valocidade;
    }
}
