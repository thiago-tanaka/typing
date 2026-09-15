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
    // A result finished by a guest, kept in the session until they log in or sign up.
    private const RESULTADO_PENDENTE = 'resultado_pendente';

    public function index($unidade = 1, $licao = 1)
    {
        $lesson = Lesson::whereHas('unit', function ($query) use ($unidade) {
            $query->where('name', (string) $unidade);
        })->where('name', (string) $licao)->firstOrFail();

        $resultadoPendente = Auth::check() ? $this->salvarResultadoPendente() : null;

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
            'resultadoPendente' => $resultadoPendente,
            'lessonProps' => [
                'unit' => $unidade,
                'lesson' => $licao,
                'lines' => [$lesson->text1, $lesson->text2, $lesson->text3, $lesson->text4],
                'saveUrl' => url("/registra/{$unidade}/{$licao}"),
                'canSave' => Auth::check(),
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

        $dados = request()->validate([
            'licao_velocidade' => ['required', 'integer', 'min:0', 'max:'.Digitacao::VELOCIDADE_MAXIMA],
            'licao_precisao' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $velocidade = (int) $dados['licao_velocidade'];
        $precisao = (int) $dados['licao_precisao'];

        if (! Auth::check()) {
            session()->put(self::RESULTADO_PENDENTE, [
                'lesson_id' => $lesson->id,
                'velocidade' => $velocidade,
                'precisao' => $precisao,
            ]);
            // After logging in, come back to this lesson.
            session()->put('url.intended', url("/{$unidade}/{$licao}"));

            return request()->wantsJson()
                ? response()->json(['saved' => false, 'best' => null, 'pending' => true])
                : redirect()->back();
        }

        $saved = $this->salvarResultado($lesson, $velocidade, $precisao);

        if (request()->wantsJson()) {
            $best = Pontuacao::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first();

            return response()->json([
                'saved' => $saved,
                'best' => $best ? ['velocidade' => (int) $best->velocidade, 'precisao' => (int) $best->precisao] : null,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Stores the result for the logged-in user when it beats their best score.
     */
    private function salvarResultado(Lesson $lesson, int $velocidade, int $precisao): bool
    {
        if (! (new PontuacaoNovaEMaiorAction)($lesson, $velocidade, $precisao)) {
            return false;
        }

        Pontuacao::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['velocidade' => $velocidade, 'precisao' => $precisao]
        );

        return true;
    }

    /**
     * Saves the result the user finished as a guest, the first time a lesson
     * page opens after they log in (or verify their email).
     */
    private function salvarResultadoPendente(): ?array
    {
        $pendente = session()->pull(self::RESULTADO_PENDENTE);
        $lesson = $pendente ? Lesson::with('unit')->find($pendente['lesson_id']) : null;

        if (! $lesson) {
            return null;
        }

        return [
            'unidade' => $lesson->unit->name,
            'licao' => $lesson->name,
            'velocidade' => $pendente['velocidade'],
            'precisao' => $pendente['precisao'],
            'salvo' => $this->salvarResultado($lesson, $pendente['velocidade'], $pendente['precisao']),
        ];
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
