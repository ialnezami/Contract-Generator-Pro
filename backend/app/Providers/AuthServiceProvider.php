<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for contract access
        Gate::define('view-contract', function ($user, $contract) {
            return $user->id === $contract->user_id || $user->isAdmin();
        });

        Gate::define('update-contract', function ($user, $contract) {
            return $user->id === $contract->user_id || $user->isAdmin();
        });

        Gate::define('delete-contract', function ($user, $contract) {
            return $user->id === $contract->user_id || $user->isAdmin();
        });

        // Define gates for template access
        Gate::define('view-template', function ($user, $template) {
            return $template->is_public || $user->id === $template->user_id || $user->isAdmin();
        });

        Gate::define('update-template', function ($user, $template) {
            return $user->id === $template->user_id || $user->isAdmin();
        });

        Gate::define('delete-template', function ($user, $template) {
            return $user->id === $template->user_id || $user->isAdmin();
        });

        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
