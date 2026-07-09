<?php

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;

test('super admin can invite a user to a new company', function () {
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
    ]);

    $response = $this->actingAs($superAdmin)
        ->post(route('invitations.store'), [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'new_company_name' => 'Acme Corporation',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('companies', ['name' => 'Acme Corporation']);

    $company = Company::where('name', 'Acme Corporation')->first();
    $this->assertDatabaseHas('invitations', [
        'email' => 'newadmin@example.com',
        'role' => 'admin',
        'company_id' => $company->id,
        'new_company_name' => 'Acme Corporation',
        'invited_by_id' => $superAdmin->id,
    ]);
});

test('super admin can invite a user to an existing company', function () {
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
    ]);

    $company = Company::create(['name' => 'Existing Company']);

    $response = $this->actingAs($superAdmin)
        ->post(route('invitations.store'), [
            'email' => 'member@example.com',
            'role' => 'member',
            'company_id' => $company->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('invitations', [
        'email' => 'member@example.com',
        'role' => 'member',
        'company_id' => $company->id,
        'new_company_name' => null,
        'invited_by_id' => $superAdmin->id,
    ]);
});

test('invited user can accept invitation and is associated with the correct company without duplicate creation', function () {
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
    ]);

    $company = Company::create(['name' => 'Target Company']);

    $invitation = Invitation::create([
        'email' => 'invitee@example.com',
        'role' => 'admin',
        'company_id' => $company->id,
        'new_company_name' => null,
        'invited_by_id' => $superAdmin->id,
        'token' => 'test-token-123',
    ]);

    // Accept invitation
    $response = $this->post(route('invitations.accept', $invitation->token), [
        'name' => 'Accepted User',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'invitee@example.com',
        'name' => 'Accepted User',
        'role' => 'admin',
        'company_id' => $company->id,
    ]);

    // Assert only one company exists with this name
    $this->assertEquals(1, Company::where('name', 'Target Company')->count());
});
