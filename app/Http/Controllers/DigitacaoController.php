<?php

namespace App\Http\Controllers;

use App\Actions\GetPontuacoesAction;
use App\Actions\GetPontuacoesGraficoAction;
use App\Actions\PontuacaoNovaEMaiorAction;
use App\Models\Lesson;
use App\Models\Pontuacao;
use Illuminate\Http\JsonResponse;
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

    public function update($unidade, $licao): RedirectResponse|JsonResponse
    {
        $lesson = Lesson::whereHas('unit', function ($query) use ($unidade) {
            $query->where('name', $unidade);
        })->where('name', $licao)->firstOrFail();

        $saved = false;

        if (Auth::check() && (new PontuacaoNovaEMaiorAction)(
            $lesson,
            request('licao_velocidade'),
            request('licao_precisao')
        )) {
            Pontuacao::updateOrCreate(
                ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
                ['velocidade' => request('licao_velocidade'), 'precisao' => request('licao_precisao')]
            );
            $saved = true;
        }

        if (request()->wantsJson()) {
            $best = Auth::check()
                ? Pontuacao::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first()
                : null;

            return response()->json([
                'saved' => $saved,
                'best' => $best ? ['velocidade' => (int) $best->velocidade, 'precisao' => (int) $best->precisao] : null,
            ]);
        }

        return redirect()->back();
    }
}
