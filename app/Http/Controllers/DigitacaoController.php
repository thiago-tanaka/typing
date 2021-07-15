<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DigitacaoController extends Controller
{
    public function index($unidade = 1, $licao = 1)
    {
        $texto = config("licoes.$unidade.$licao");
        return view('index', compact('texto', 'unidade', 'licao'));
    }

    public function update($unidade, $licao): RedirectResponse
    {
        if (Auth::check()) {
            $licao = 'licao_' . $unidade . '_' . $licao;
            auth()->user()->update([
                $licao => request('licao')
            ]);
        }
        return redirect()->back();
    }
}
