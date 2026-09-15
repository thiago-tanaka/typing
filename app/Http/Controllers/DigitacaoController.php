<?php

namespace App\Http\Controllers;

use App\Actions\GetPontuacoesAction;
use App\Actions\PontuacaoNovaEMaiorAction;
use App\Models\Digitacao;
use App\Models\Lesson;
use App\Models\Pontuacao;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DigitacaoController extends Controller
{
    public function index($unidade = 1, $licao = 1)
    {
        $lesson = Lesson::whereHas('unit', function ($query) use ($unidade) {
            $query->where('name', (string) $unidade);
        })->where('name', (string) $licao)->firstOrFail();

        $unidade = (int) $unidade;
        $licao = (int) $licao;

        $units = Unit::with(['lessons' => fn ($query) => $query->select('id', 'unit_id', 'name')])
            ->get()
            ->sortBy(fn (Unit $unit) => (int) $unit->name)
            ->values();

        $pontuacoes = Auth::check() ? (new GetPontuacoesAction)((string) $unidade) : [];

        return view('index', [
            'unidade' => $unidade,
            'licao' => $licao,
            'units' => $units,
            'pontuacoes' => $pontuacoes,
            'niveis' => Digitacao::niveis(),
            'lessonProps' => [
                'unit' => $unidade,
                'lesson' => $licao,
                'lines' => [$lesson->text1, $lesson->text2, $lesson->text3, $lesson->text4],
                'saveUrl' => Auth::check() ? url("/registra/{$unidade}/{$licao}") : null,
                'nextUrl' => $this->nextLessonUrl($units, $unidade, $licao),
                'loginUrl' => route('login'),
                'registerUrl' => Route::has('register') ? route('register') : null,
                'best' => $pontuacoes[$licao] ?? null,
                'levels' => Digitacao::niveis(),
            ],
        ]);
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

    /**
     * The next lesson of the same unit or, after its last lesson, the first
     * lesson of the next unit.
     */
    private function nextLessonUrl(Collection $units, int $unidade, int $licao): ?string
    {
        $numbers = fn (Unit $unit) => $unit->lessons->map(fn (Lesson $lesson) => (int) $lesson->name);

        $current = $units->first(fn (Unit $unit) => (int) $unit->name === $unidade);
        $nextLesson = $current ? $numbers($current)->filter(fn (int $number) => $number > $licao)->min() : null;

        if ($nextLesson !== null) {
            return url("/{$unidade}/{$nextLesson}");
        }

        $nextUnit = $units->first(fn (Unit $unit) => (int) $unit->name > $unidade && $unit->lessons->isNotEmpty());

        return $nextUnit ? url("/{$nextUnit->name}/{$numbers($nextUnit)->min()}") : null;
    }
}
