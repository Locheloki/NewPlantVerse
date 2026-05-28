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

test('users can bulk delete only their own plants', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $firstPlant = Plant::create([
        'user_id' => $user->id,
        'name' => 'First Plant',
        'species' => 'Test Species',
        'care_consistency' => 0,
        'is_neglected' => false,
    ]);
    $secondPlant = Plant::create([
        'user_id' => $user->id,
        'name' => 'Second Plant',
        'species' => 'Test Species',
        'care_consistency' => 0,
        'is_neglected' => false,
    ]);
    $otherPlant = Plant::create([
        'user_id' => $otherUser->id,
        'name' => 'Other Plant',
        'species' => 'Test Species',
        'care_consistency' => 0,
        'is_neglected' => false,
    ]);

    CareTask::create([
        'plant_id' => $firstPlant->id,
        'type' => 'Water',
        'frequency_days' => 7,
        'last_completed' => now(),
    ]);

    $this->actingAs($user)
        ->delete(route('plants.bulk-destroy'), [
            'plant_ids' => [$firstPlant->id, $secondPlant->id, $otherPlant->id],
        ])
        ->assertRedirect(route('plants.index'));

    $this->assertDatabaseMissing('plants', ['id' => $firstPlant->id]);
    $this->assertDatabaseMissing('plants', ['id' => $secondPlant->id]);
    $this->assertDatabaseMissing('care_tasks', ['plant_id' => $firstPlant->id]);
    $this->assertDatabaseHas('plants', ['id' => $otherPlant->id]);
});

test('my plants can be filtered by due care task and category', function () {
    $user = User::factory()->create();

    $vegetable = Plant::create([
        'user_id' => $user->id,
        'name' => 'Tomato',
        'species' => 'Solanum lycopersicum',
        'category' => 'vegetable',
        'care_consistency' => 50,
        'is_neglected' => false,
    ]);
    $fruit = Plant::create([
        'user_id' => $user->id,
        'name' => 'Mango',
        'species' => 'Mangifera indica',
        'category' => 'fruit',
        'care_consistency' => 90,
        'is_neglected' => false,
    ]);

    CareTask::create([
        'plant_id' => $vegetable->id,
        'type' => 'Water',
        'frequency_days' => 7,
        'last_completed' => now()->subDays(8),
    ]);
    CareTask::create([
        'plant_id' => $fruit->id,
        'type' => 'Water',
        'frequency_days' => 7,
        'last_completed' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('plants.index', ['care_filter' => 'water', 'category' => 'vegetable']))
        ->assertOk()
        ->assertSee('Tomato')
        ->assertDontSee('Mango');
});
