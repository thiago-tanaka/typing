<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/{unidade?}/{licao?}', 'App\Http\Controllers\DigitacaoController@index')
    ->where('unidade', '[1-5]+')
    ->where('licao', '[1-5]+')
    ->name('index')
    ->middleware('verifiedifauth');


Route::post('/registra/{unidade}/{licao}', 'App\Http\Controllers\DigitacaoController@update');

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['admin']], function () {
        Route::get('/settings', 'App\Http\Controllers\SettingsController@index')->name('settings.index');
        Route::put('/lesson/{lesson}', 'App\Http\Controllers\LessonController@update')->name('lesson.update');
    });
});
Route::get('/units', 'App\Http\Controllers\UnitController@index')->name('units.index');

