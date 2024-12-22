<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;

test('a user can register for an account', function () {
    $this->browse(function (Browser $browser) {
        $user = null;

        $browser
            ->visitRoute('register')
            ->type('#name', 'Mark Wilkinson')
            ->type('#email', $email = 'mark@example.com')
            ->type('#password', $password = 'password')
            ->type('#password_confirmation', $password)
            ->press('@register')
            ->waitForRoute('tasks.index')
            ->tap(function () use (&$user, $email) {
                $user = User::where('email', $email)->first();
            })
            ->assertAuthenticatedAs($user);
    });
});
