<?php

use App\Models\CareTask;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('authenticated users can view their plant pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/my-plants')
        ->assertOk();
});

test('admins can view the admin dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('logging care recovers care consistency by ten percent', function () {
    $user = User::factory()->create();
    $plant = Plant::create([
        'user_id' => $user->id,
        'name' => 'Test Plant',
        'species' => 'Test Species',
        'care_consistency' => 0,
        'is_neglected' => true,
    ]);
    CareTask::create([
        'plant_id' => $plant->id,
        'type' => 'Water',
        'frequency_days' => 7,
        'last_completed' => now()->subDays(30),
    ]);

    $this->actingAs($user)
        ->post(route('plants.log-care', [$plant->id, 'Water']))
        ->assertRedirect();

    expect($plant->fresh()->care_consistency)->toBe(10);
});
