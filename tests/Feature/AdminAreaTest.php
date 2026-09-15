<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login_from_settings()
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    public function test_non_admin_gets_404_on_settings()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/settings')->assertNotFound();
    }

    public function test_admin_sees_settings()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/settings');

        $response->assertOk();
        $response->assertSee('<lesson-list></lesson-list>', false);
    }

    public function test_admin_updates_lesson_texts()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($admin)->putJson("/lesson/{$lesson->id}", [
            'text1' => 'novo um',
            'text2' => 'novo dois',
            'text3' => 'novo tres',
            'text4' => 'novo quatro',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Lesson saved!');
        $response->assertJsonPath('lesson.text4', 'novo quatro');
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'text1' => 'novo um', 'text4' => 'novo quatro']);
    }

    public function test_only_admin_can_update_lessons()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['text1' => 'original']);
        $payload = ['text1' => 'x', 'text2' => 'x', 'text3' => 'x', 'text4' => 'x'];

        $this->put("/lesson/{$lesson->id}", $payload)->assertRedirect('/login');
        $this->actingAs($user)->put("/lesson/{$lesson->id}", $payload)->assertNotFound();

        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'text1' => 'original']);
    }
}
