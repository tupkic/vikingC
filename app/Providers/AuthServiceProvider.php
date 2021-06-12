<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

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

        Passport::routes();

        /** admin gate to check if user is an admin **/
        Gate::define('isAdmin', function($user){
            return $user->is_admin == 1;
        });

        /** user gate to check if user is an owner of the project **/
        Gate::define('isOwner', function ($user, $project){
            return $user->id == $project->user_id;
        });

    }
}
