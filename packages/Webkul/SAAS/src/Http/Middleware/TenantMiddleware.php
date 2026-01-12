<?php

namespace Webkul\SAAS\Http\Middleware;

use Closure;
use Webkul\SAAS\Models\Company;

class TenantMiddleware
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
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Assuming structure: tenant.domain.com
        // This logic might need adjustment based on exact domain structure (localhost vs prod)
        // For localhost testing like 'alpha.localhost', verify parts count

        // Skip for main domain or www
        // if ($parts[0] === 'www' || count($parts) < 3) {
        //     // Handle main site logic or fall through
        // }

        $subdomain = $parts[0];

        $company = Company::where('domain', $subdomain)->first();

        if ($company && $company->status) {
            session(['company_id' => $company->id]);
            // Optional: Bind to container
            // app()->instance('current_company', $company);
        } else {
            // Ideally redirect to main page or show 404 tenant not found
            // For MVP development, we might leniently allow access or strict abort
            // abort(404, 'Tenant Not Found');
        }

        return $next($request);
    }
}
