<?php

namespace App\Http\Controllers;

use App\Actions\GetPontuacoesAction;
use App\Actions\GetPontuacoesGraficoAction;
use App\Actions\PontuacaoNovaEMaiorAction;
use App\Models\Lesson;
use App\Models\Pontuacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DigitacaoController extends Controller
{
    public function index($unidade = 1, $licao = 1)
    {
        $pontuacoes = [];
        $pontuacoes_chart = [];
        if (auth()->user()) {
            $pontuacoes = (new GetPontuacoesAction)($unidade);
            $pontuacoes_chart = (new GetPontuacoesGraficoAction)($unidade);
        }

        $lesson = Lesson::whereHas('unit', function ($query) use ($unidade) {
            $query->where('name', $unidade);
        })->where('name', $licao)->firstOrFail();

        $texto = [
            1 => $lesson->text1,
            2 => $lesson->text2,
            3 => $lesson->text3,
            4 => $lesson->text4
        ];
        return view('index', compact('texto', 'unidade', 'licao', 'pontuacoes', 'pontuacoes_chart'));
    }

    public function update($unidade, $licao): RedirectResponse
    {
        $lesson = Lesson::whereHas('unit', function ($query) use ($unidade){
            $query->where('name', $unidade);
        })->where('name', $licao)->firstOrFail();

        if (Auth::check()) {
            if (
            (new PontuacaoNovaEMaiorAction)(
                $lesson,
                request('licao_velocidade'),
                request('licao_precisao'))) {
            $pontuacao = Pontuacao::updateOrCreate(
                ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
                ['velocidade' => request('licao_velocidade'), 'precisao' => request('licao_precisao')]
            );

            }
        }
        return redirect()->back();
    }
}
