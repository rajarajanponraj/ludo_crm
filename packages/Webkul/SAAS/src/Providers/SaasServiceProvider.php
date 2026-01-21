<?php

namespace Webkul\SAAS\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Webkul\SAAS\Models\SaasSubscription;
use Webkul\User\Models\User;

class SaasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 1. Feature Access Gate
        Gate::define('feature-access', function ($user, $feature) {
            // Super Admin always has access
            if ($user->is_superuser) {
                return true;
            }

            // Get current company subscription
            // Ideally cached or retrieved from session
            $companyId = $user->company_id;
            if (!$companyId)
                return false;

            $subscription = SaasSubscription::with('package')->where('company_id', $companyId)->where('status', 'active')->first();

            if (!$subscription || !$subscription->package) {
                return false;
            }

            $features = $subscription->package->features ?? [];
            return in_array($feature, $features);
        });

        // 2. Create User Limit Gate
        Gate::define('create-user', function ($user) {
            if ($user->is_superuser)
                return true;

            $companyId = $user->company_id;
            if (!$companyId)
                return false;

            $subscription = SaasSubscription::where('company_id', $companyId)->where('status', 'active')->first();
            if (!$subscription)
                return false;

            $currentUsers = User::where('company_id', $companyId)->count();

            return $currentUsers < $subscription->max_users;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php',
            'acl'
        );
    }
}
