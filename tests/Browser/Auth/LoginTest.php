<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;

test('a user can log in to their account', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser
            ->visitRoute('login')
            ->type('#email', $user->email)
            ->type('#password', 'password')
            ->press('@login')
            ->waitForRoute('tasks.index')
            ->assertAuthenticatedAs($user)
            ->assertSee("Hello, {$user->firstName}!");
    });
});
