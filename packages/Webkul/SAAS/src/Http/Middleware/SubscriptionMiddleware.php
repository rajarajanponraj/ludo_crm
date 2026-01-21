<?php

namespace Webkul\SAAS\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Webkul\SAAS\Models\SaasSubscription;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Skip for Super Admin
        if (auth()->guard('user')->check() && auth()->guard('user')->user()->is_superuser) {
            return $next($request);
        }

        $companyId = session()->get('company_id');

        if (!$companyId) {
            // Should be handled by TenantMiddleware, but safety check
            return $next($request);
        }

        $subscription = SaasSubscription::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', Carbon::now())
            ->first();

        // If no active subscription found, and not on a "safe" route (like billing)
        if (!$subscription) {
            if ($request->routeIs('saas.billing.*') || $request->routeIs('saas.companies.*')) {
                return $next($request);
            }

            // Redirect to billing page (placeholder for now)
            return redirect()->route('saas.billing.index')->with('warning', 'Your subscription has expired. Please upgrade.');
        }

        // Share subscription with views
        view()->share('currentSubscription', $subscription);

        return $next($request);
    }
}
