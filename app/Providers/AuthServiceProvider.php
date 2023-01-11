<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AuthServiceProvider extends ServiceProvider
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

        Validator::extend('date_difference', function ($attribute, $value, $parameters, $validator) {
            $firstDate = Carbon::parse($parameters[0]);
            $secondDate = Carbon::parse($parameters[1]);
            $minDifference = $parameters[2];
            if($firstDate->diff($secondDate) < $minDifference)
                return false;
            return true;
        });

        //
    }
}
