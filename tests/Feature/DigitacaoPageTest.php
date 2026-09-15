<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Pontuacao;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\LessonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigitacaoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_first_lesson_on_home_page()
    {
        $this->lesson('1', '1', 'alfa');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('alfa um');
        $response->assertSee('alfa quatro');
        $response->assertSee('data-vue="typing-lesson"', false);
        $response->assertSee('"saveUrl":null');
        $response->assertSee('250+ CPM · 98%+');
    }

    public function test_guest_sees_requested_unit_and_lesson()
    {
        $this->lesson('1', '1', 'alfa');
        $this->lesson('2', '3', 'beta');

        $response = $this->get('/2/3');

        $response->assertOk();
        $response->assertSee('beta um');
        $response->assertDontSee('alfa um');
    }

    public function test_url_outside_route_pattern_returns_404()
    {
        $this->lesson('1', '1');

        $this->get('/99/99')->assertNotFound();
    }

    public function test_missing_lesson_returns_404()
    {
        $this->lesson('1', '1');

        $this->get('/1/5')->assertNotFound();
    }

    public function test_every_seeded_lesson_page_opens()
    {
        $this->seed(LessonSeeder::class);

        $this->assertSame(count(config('licoes')), Unit::count());

        foreach (config('licoes') as $unidade => $licoes) {
            foreach (array_keys($licoes) as $licao) {
                $this->get("/$unidade/$licao")->assertOk();
            }
        }
    }

    public function test_verified_user_sees_own_scores_and_can_save()
    {
        $user = User::factory()->create();
        $licao1 = $this->lesson('1', '1');
        $licao2 = $this->lesson('1', '2');
        Pontuacao::factory()->for($licao1)->create(['user_id' => $user->id, 'velocidade' => '260', 'precisao' => '99']);
        Pontuacao::factory()->for($licao2)->create(['velocidade' => '100', 'precisao' => '97']);

        $response = $this->actingAs($user)->get('/1/1');

        $response->assertOk();
        $response->assertSee('260 · 99%');
        $response->assertSee('data-level="excellent"', false);
        $response->assertDontSee('100 · 97%');
        $response->assertDontSee('data-level="average"', false);
        $response->assertSee('"best":{"velocidade":260,"precisao":99,"nivel":"excellent"}');
        $response->assertSee('"saveUrl":"http://localhost/registra/1/1"');
    }

    public function test_lesson_page_links_to_the_next_lesson()
    {
        $this->lesson('1', '1');
        $this->lesson('1', '2');
        $this->lesson('2', '1');

        $this->get('/1/1')->assertSee('"nextUrl":"http://localhost/1/2"');
        $this->get('/1/2')->assertSee('"nextUrl":"http://localhost/2/1"');
        $this->get('/2/1')->assertSee('"nextUrl":null');
    }

    public function test_unverified_user_is_redirected_to_email_verification()
    {
        $user = User::factory()->unverified()->create();
        $this->lesson('1', '1');

        $this->actingAs($user)->get('/1/1')->assertRedirect('email/verify');
    }

    private function lesson(string $unidade, string $licao, string $texto = 'texto'): Lesson
    {
        $unit = Unit::firstOrCreate(['name' => $unidade]);

        return Lesson::factory()->for($unit)->create([
            'name' => $licao,
            'text1' => "$texto um",
            'text2' => "$texto dois",
            'text3' => "$texto tres",
            'text4' => "$texto quatro",
        ]);
    }
}
