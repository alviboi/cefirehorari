<?php

namespace App\Providers;

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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
                /* define a admin user role */

                Gate::define('esAdmin', function($user) {

                    return $user->Perfil == 1;

                 });



                 /* define a manager user role */

                 Gate::define('esAssessor', function($user) {

                     return $user->Perfil == 0;

                 });



                 /* define a user role */

                 Gate::define('esAdministratiu', function($user) {

                     return $user->Perfil == 2;

                 });
    }
}
