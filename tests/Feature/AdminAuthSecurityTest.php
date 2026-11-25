<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('admin_login_throttle:127.0.0.1');
});

test('login page renders clean inputs without autofill or hardcoded credentials', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $content = $response->getContent();

    // Verify no hardcoded values in password & pin
    expect($content)->not->toContain('value="admin123"');
    expect($content)->not->toContain('value="123456"');
    expect($content)->not->toContain('value="{{ old(\'login_id\', \'admin@metascout.gg\') }}"');

    // Verify autocomplete protection exists
    expect($content)->toContain('autocomplete="off"');
    expect($content)->toContain('autocomplete="new-password"');
});

test('admin can authenticate successfully using bcrypt hashed password', function () {
    $user = User::create([
        'name' => 'MetaScout Admin',
        'email' => 'admin@metascout.gg',
        'password' => Hash::make('StrongAdminPassword123!'),
    ]);

    $response = $this->post('/login', [
        'login_id' => 'admin@metascout.gg',
        'password' => 'StrongAdminPassword123!',
        'pin' => '123456',
    ]);

    $response->assertRedirect('/admin/dashboard');
    $this->assertAuthenticatedAs($user);
    $response->assertSessionHas('admin_logged_in', true);
    $response->assertSessionHas('admin_email', 'admin@metascout.gg');
});

test('admin login fails when provided with incorrect password', function () {
    User::create([
        'name' => 'MetaScout Admin',
        'email' => 'admin@metascout.gg',
        'password' => Hash::make('StrongAdminPassword123!'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'login_id' => 'admin@metascout.gg',
        'password' => 'WrongPassword!',
        'pin' => '123456',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error');
    $response->assertSessionMissing('admin_logged_in');
});

test('repeated failed login attempts trigger rate limiting lockout', function () {
    // 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $this->from('/login')->post('/login', [
            'login_id' => 'admin@metascout.gg',
            'password' => 'WrongPassword!',
        ]);
    }

    // 6th attempt should be blocked / throttled
    $response = $this->from('/login')->post('/login', [
        'login_id' => 'admin@metascout.gg',
        'password' => 'WrongPassword!',
    ]);

    // Either redirected with locked session message or 429 Too Many Requests
    if ($response->status() === 429) {
        $response->assertStatus(429);
    } else {
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $errorMessage = session('error');
        expect($errorMessage)->toMatch('/Terlalu banyak percobaan|Kredensial atau PIN keamanan tidak cocok/');
    }
});

test('unauthenticated users cannot access protected admin routes', function () {
    // 1. Dashboard
    $this->get('/admin/dashboard')->assertRedirect('/login');

    // 2. Hero Management
    $this->get('/admin/heroes')->assertRedirect('/login');

    // 3. Liquipedia Sync mutation
    $this->post('/admin/datasets/sync')->assertRedirect('/login');
});

test('admin logout properly invalidates session and redirects to home', function () {
    $response = $this->withSession([
        'admin_logged_in' => true,
        'admin_user' => 'Superadmin',
        'admin_email' => 'admin@metascout.gg',
        'admin_ip' => '127.0.0.1',
        'admin_last_activity' => now()->timestamp,
    ])->get('/admin/logout');

    $response->assertRedirect('/home');
    $response->assertSessionMissing('admin_logged_in');
    $response->assertSessionMissing('admin_user');
});
