<?php

use App\Models\User;

test('user can update time format preference', function () {
    $user = User::factory()->create([
        'settings' => ['time_format' => '24'],
        'time_offset' => 0
    ]);

    $response = $this
        ->actingAs($user)
        ->put('/profile/settings', [
            'time_format' => '12',
            'time_offset' => 0,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile')
        ->assertSessionHas('status', 'settings-updated');

    $user->refresh();
    expect($user->getTimeFormat())->toBe('12');
});

test('user time format defaults to 24 hour', function () {
    $user = User::factory()->create();
    
    expect($user->getTimeFormat())->toBe('24');
});

test('user can format time according to preference', function () {
    $user = User::factory()->create([
        'settings' => ['time_format' => '12']
    ]);
    
    $time = '14:30:45';
    $formatted = $user->formatTime($time);
    
    expect($formatted)->toBe('2:30:45 PM');
    
    // Test 24-hour format
    $user->settings = ['time_format' => '24'];
    $user->save();
    
    $formatted = $user->formatTime($time);
    expect($formatted)->toBe('14:30:45');
});

test('time format validation rejects invalid values', function () {
    $user = User::factory()->create([
        'time_offset' => 0
    ]);

    $response = $this
        ->actingAs($user)
        ->put('/profile/settings', [
            'time_format' => 'invalid',
            'time_offset' => 0,
        ]);

    $response->assertSessionHasErrors(['time_format']);
});
