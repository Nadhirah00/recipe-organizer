<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the landing page to guests', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Save, explore and plan with')
        ->assertSee('Get started')
        ->assertSee('Log in')
        ->assertSee('Create account');
});

it('sends authenticated users to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect('/dashboard');
});
