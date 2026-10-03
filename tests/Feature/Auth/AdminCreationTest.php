<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('registration is disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', ['name' => 'x', 'email' => 'x@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])
        ->assertStatus(404);
    $this->assertDatabaseMissing('users', ['email' => 'x@example.com']);
});

test('admin:create prompts for details and creates a verified admin', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Jane Admin')
        ->expectsQuestion('Email', 'jane@example.com')
        ->expectsQuestion('Password (min 8 characters)', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-strong-password')
        ->assertSuccessful();

    $user = User::firstWhere('email', 'jane@example.com');
    expect($user->name)->toBe('Jane Admin')
        ->and(Hash::check('a-strong-password', $user->password))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();

    // The new admin can reach the dashboard.
    $this->actingAs($user)->get('/admin')->assertOk();
});

test('admin:create refuses mismatched passwords', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Jane')
        ->expectsQuestion('Email', 'jane2@example.com')
        ->expectsQuestion('Password (min 8 characters)', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'different-password')
        ->assertFailed();

    $this->assertDatabaseMissing('users', ['email' => 'jane2@example.com']);
});

test('admin:create refuses a duplicate email or short password', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->artisan('admin:create', ['--name' => 'Dup', '--email' => 'taken@example.com', '--password' => 'long-enough-pw'])
        ->assertFailed();
    $this->artisan('admin:create', ['--name' => 'Short', '--email' => 'short@example.com', '--password' => 'abc'])
        ->assertFailed();

    expect(User::where('email', 'short@example.com')->exists())->toBeFalse();
});
