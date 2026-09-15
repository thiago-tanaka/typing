<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Pontuacao;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistraPontuacaoTest extends TestCase
{
    use RefreshDatabase;

    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $unit = Unit::factory()->create(['name' => '1']);
        $this->lesson = Lesson::factory()->for($unit)->create(['name' => '1']);
    }

    public function test_first_score_is_saved_and_user_goes_back()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/1/1')
            ->post('/registra/1/1', ['licao_velocidade' => '120', 'licao_precisao' => '97']);

        $response->assertRedirect('/1/1');
        $this->assertDatabaseHas('pontuacoes', [
            'user_id' => $user->id,
            'lesson_id' => $this->lesson->id,
            'velocidade' => '120',
            'precisao' => '97',
        ]);
    }

    public function test_score_is_replaced_when_speed_and_accuracy_are_not_lower()
    {
        $user = User::factory()->create();
        $this->score($user, '120', '97');

        $this->actingAs($user)->post('/registra/1/1', ['licao_velocidade' => '120', 'licao_precisao' => '98']);

        $this->assertDatabaseCount('pontuacoes', 1);
        $this->assertDatabaseHas('pontuacoes', ['user_id' => $user->id, 'velocidade' => '120', 'precisao' => '98']);
    }

    public function test_score_is_kept_when_speed_or_accuracy_is_lower()
    {
        $user = User::factory()->create();
        $this->score($user, '120', '97');

        $this->actingAs($user)->post('/registra/1/1', ['licao_velocidade' => '200', 'licao_precisao' => '96']);
        $this->actingAs($user)->post('/registra/1/1', ['licao_velocidade' => '100', 'licao_precisao' => '99']);

        $this->assertDatabaseCount('pontuacoes', 1);
        $this->assertDatabaseHas('pontuacoes', ['user_id' => $user->id, 'velocidade' => '120', 'precisao' => '97']);
    }

    public function test_scores_are_compared_as_numbers_not_as_text()
    {
        $user = User::factory()->create();
        $this->score($user, '95', '99');

        $this->actingAs($user)->post('/registra/1/1', ['licao_velocidade' => '100', 'licao_precisao' => '100']);

        $this->assertDatabaseHas('pontuacoes', ['user_id' => $user->id, 'velocidade' => '100', 'precisao' => '100']);
    }

    public function test_guest_score_is_ignored_but_still_redirected()
    {
        $response = $this->from('/1/1')
            ->post('/registra/1/1', ['licao_velocidade' => '120', 'licao_precisao' => '97']);

        $response->assertRedirect('/1/1');
        $this->assertDatabaseCount('pontuacoes', 0);
    }

    public function test_unknown_lesson_returns_404()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/registra/4/4', ['licao_velocidade' => '120', 'licao_precisao' => '97'])
            ->assertNotFound();
    }

    private function score(User $user, string $velocidade, string $precisao): Pontuacao
    {
        return Pontuacao::factory()->for($this->lesson)->create([
            'user_id' => $user->id,
            'velocidade' => $velocidade,
            'precisao' => $precisao,
        ]);
    }
}
