<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/{unidade?}/{licao?}', 'App\Http\Controllers\DigitacaoController@index')
    ->where('unidade', '[1-5]+')
    ->where('licao', '[1-5]+')
    ->name('index');


Route::post('/registra/{unidade}/{licao}', 'App\Http\Controllers\DigitacaoController@update');
