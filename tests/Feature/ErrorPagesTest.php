<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_page_shows_branded_404(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('Page not found')->assertSee('Back to home');
        $this->get('/shop/no-such-product')->assertNotFound()->assertSee('Page not found');
    }

    public function test_guest_on_unknown_admin_url_goes_to_login(): void
    {
        $this->get('/admin/nonsense')->assertRedirect(route('login'));
    }

    public function test_signed_in_admin_still_sees_404(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/nonsense')->assertNotFound();
    }

    public function test_get_on_post_only_url_redirects_to_contact(): void
    {
        $this->get('/send-message/')->assertRedirect(route('contact'));
    }

    public function test_expired_csrf_token_redirects_back_with_message(): void
    {
        Route::post('/_csrf-test', fn () => throw new TokenMismatchException)->middleware('web');

        $this->from('/contact')->post('/_csrf-test')
            ->assertRedirect('/contact')
            ->assertSessionHas('error');
    }

    public function test_500_and_other_error_views_render(): void
    {
        foreach ([401, 403, 404, 405, 419, 429, 500, 503] as $code) {
            $this->assertStringContainsString((string) $code, view("errors.$code")->render());
        }
    }
}
