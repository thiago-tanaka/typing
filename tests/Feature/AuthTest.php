<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_logs_in_and_sends_verification_email()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $user = User::where('email', 'maria@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email_verified_at);
        $this->assertFalse((bool) $user->is_admin);
        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            return str_contains($notification->toMail($user)->actionUrl, "/email/verify/{$user->id}/");
        });
    }

    public function test_verification_email_goes_through_the_mailer()
    {
        $sent = 0;
        Event::listen(MessageSent::class, function () use (&$sent) {
            $sent++;
        });

        $response = $this->post('/register', [
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertSame(1, $sent);
    }

    public function test_unverified_user_sees_verification_notice()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertOk();
        $response->assertSee('Verify Your Email Address');
    }

    public function test_verification_link_marks_email_as_verified()
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect('/');
        $response->assertSessionHas('verified', true);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_login_and_logout()
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected()
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'errada']);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
