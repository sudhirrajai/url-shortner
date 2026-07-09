<?php

use App\Models\Company;
use App\Models\User;
use App\Models\ShortUrl;

beforeEach(function () {
    $this->companyA = Company::create(['name' => 'Company A']);
    $this->companyB = Company::create(['name' => 'Company B']);

    $this->adminA = User::create([
        'name' => 'Admin A',
        'email' => 'adminA@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'company_id' => $this->companyA->id
    ]);

    $this->memberA = User::create([
        'name' => 'Member A',
        'email' => 'memberA@example.com',
        'password' => bcrypt('password'),
        'role' => 'member',
        'company_id' => $this->companyA->id
    ]);

    $this->superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin_test@example.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin'
    ]);
});

// 1. Admin and Member CAN create short urls
test('Admin can create short urls', function () {
    $response = $this->actingAs($this->adminA)
        ->post(route('short-urls.store'), ['original_url' => 'https://laravel.com']);

    $response->assertRedirect();
    $this->assertDatabaseHas('short_urls', ['original_url' => 'https://laravel.com', 'company_id' => $this->companyA->id]);
});

test('Member can create short urls', function () {
    $response = $this->actingAs($this->memberA)
        ->post(route('short-urls.store'), ['original_url' => 'https://laravel.com']);

    $response->assertRedirect();
    $this->assertDatabaseHas('short_urls', ['original_url' => 'https://laravel.com', 'company_id' => $this->companyA->id]);
});

// 2. SuperAdmin cannot create short urls
test('SuperAdmin cannot create short urls', function () {
    $response = $this->actingAs($this->superAdmin)
        ->post(route('short-urls.store'), ['original_url' => 'https://laravel.com']);

    $response->assertStatus(403);
});

// 3. Admin and Member see all short urls in their own company
test('Admin and Member see company short urls', function () {
    // Create URL in Company A
    ShortUrl::create([
        'original_url' => 'https://google.com',
        'short_code' => 'googla',
        'company_id' => $this->companyA->id,
        'user_id' => $this->memberA->id,
    ]);

    // Create URL in Company B
    ShortUrl::create([
        'original_url' => 'https://apple.com',
        'short_code' => 'applco',
        'company_id' => $this->companyB->id,
        'user_id' => $this->superAdmin->id, // or another user
    ]);

    $response = $this->actingAs($this->adminA)
        ->get(route('short-urls.index'));

    $response->assertStatus(200);
    $response->assertSee('https://google.com');
    $response->assertDontSee('https://apple.com');
});

// 4. Short urls are publicly resolvable
test('Guest can resolve short urls', function () {
    $url = ShortUrl::create([
        'original_url' => 'https://google.com',
        'short_code' => 'googla',
        'company_id' => $this->companyA->id,
        'user_id' => $this->memberA->id,
    ]);

    $response = $this->get(route('short-urls.resolve', 'googla'));
    $response->assertRedirect('https://google.com');
});

// 5. Member can only see the list of all short urls created by themselves
test('Member can only see short urls created by themselves', function () {
    // Create URL created by Admin A (same company)
    ShortUrl::create([
        'original_url' => 'https://admin-created.com',
        'short_code' => 'adminc',
        'company_id' => $this->companyA->id,
        'user_id' => $this->adminA->id,
    ]);

    // Create URL created by Member A
    ShortUrl::create([
        'original_url' => 'https://member-created.com',
        'short_code' => 'membc',
        'company_id' => $this->companyA->id,
        'user_id' => $this->memberA->id,
    ]);

    $response = $this->actingAs($this->memberA)
        ->get(route('short-urls.index'));

    $response->assertStatus(200);
    $response->assertSee('https://member-created.com');
    $response->assertDontSee('https://admin-created.com');
});
