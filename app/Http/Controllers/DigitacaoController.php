<?php

namespace App\Http\Controllers;

use App\Actions\GetPontuacoesAction;
use App\Actions\PontuacaoNovaEMaiorAction;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DigitacaoController extends Controller
{
    public function index($unidade = 1, $licao = 1)
    {
        $pontuacoes = [];
        if (auth()->user()) {
            $pontuacoes = (new GetPontuacoesAction)($unidade);
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
        return view('index', compact('texto', 'unidade', 'licao', 'pontuacoes'));
    }

    public function update($unidade, $licao): RedirectResponse
    {
        if (Auth::check()) {
            $licao = 'licao_' . $unidade . '_' . $licao;
            if ((new PontuacaoNovaEMaiorAction)(auth()->user()->$licao, request('licao'))) {
                auth()->user()->update([
                    $licao => request('licao')
                ]);
            }
        }
        return redirect()->back();
    }
}
