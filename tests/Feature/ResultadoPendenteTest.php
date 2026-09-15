<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Pontuacao;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultadoPendenteTest extends TestCase
{
    use RefreshDatabase;

    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->for(Unit::factory()->create(['name' => '2']))->create(['name' => '3']);
        Lesson::factory()->for(Unit::factory()->create(['name' => '1']))->create(['name' => '1']);
    }

    public function test_result_typed_as_a_guest_is_saved_after_login()
    {
        $user = User::factory()->create();

        $this->postJson('/registra/2/3', ['licao_velocidade' => 200, 'licao_precisao' => 98])
            ->assertExactJson(['saved' => false, 'best' => null, 'pending' => true]);
        $this->assertDatabaseCount('pontuacoes', 0);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/2/3');

        $this->get('/2/3')->assertSee('Your result on Unit 2 · Lesson 3 (200 CPM · 98%) was saved to your account.');
        $this->assertDatabaseHas('pontuacoes', [
            'user_id' => $user->id,
            'lesson_id' => $this->lesson->id,
            'velocidade' => '200',
            'precisao' => '98',
        ]);

        $this->get('/2/3')->assertDontSee('Your result on Unit 2');
    }

    public function test_result_typed_as_a_guest_does_not_replace_a_better_score()
    {
        $user = User::factory()->create();
        Pontuacao::factory()->for($this->lesson)->create(['user_id' => $user->id, 'velocidade' => '300', 'precisao' => '99']);

        $this->postJson('/registra/2/3', ['licao_velocidade' => 100, 'licao_precisao' => 96]);

        $this->actingAs($user)->get('/1/1')->assertSee('did not beat your best score');
        $this->assertDatabaseHas('pontuacoes', ['user_id' => $user->id, 'velocidade' => '300', 'precisao' => '99']);
    }

    public function test_result_waits_until_the_email_is_verified()
    {
        $user = User::factory()->unverified()->create();

        $this->postJson('/registra/2/3', ['licao_velocidade' => 200, 'licao_precisao' => 98]);

        $this->actingAs($user)->get('/1/1')->assertRedirect('email/verify');
        $this->assertDatabaseCount('pontuacoes', 0);

        $user->markEmailAsVerified();

        $this->actingAs($user)->get('/1/1')->assertSee('was saved to your account');
        $this->assertDatabaseCount('pontuacoes', 1);
    }
}
