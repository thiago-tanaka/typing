<?php

namespace App\Http\Controllers;

use App\Actions\GetPontuacoesAction;
use App\Actions\PontuacaoAtualEMaiorAction;
use App\Actions\PontuacaoNovaEMaiorAction;
use App\Actions\SetColorAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DigitacaoController extends Controller
{
    public function index($unidade = 1, $licao = 1)
    {
        $pontuacoes = [];
        if(auth()->user()){
            $pontuacoes = (new GetPontuacoesAction)($unidade);
        }

        $texto = config("licoes.$unidade.$licao");
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
