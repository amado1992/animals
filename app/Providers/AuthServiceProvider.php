<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*ResetPassword::createUrlUsing(function ($user, string $token) {
            if ($user->hasRole('website-user'))
                return 'http://www-tst.zoo-services.com/reset-password?token='.$token.'&email='.$user->email;
            else
                return 'https://app.zoo-services.com/password/reset/'.$token.'?email='.$user->email;
        });*/
    }
}
