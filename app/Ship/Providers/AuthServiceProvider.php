<?php

namespace App\Ship\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Ship\Abstracts\Providers\AuthServiceProvider as AbstractAuthServiceProvider;

class AuthServiceProvider extends AbstractAuthServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
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
    }
}
