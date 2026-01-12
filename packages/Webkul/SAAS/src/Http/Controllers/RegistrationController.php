<?php

namespace Webkul\SAAS\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\SAAS\Repositories\CompanyRepository;
use Webkul\User\Models\User;

class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CompanyRepository $companyRepository)
    {
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('saas::registration.index');
    }

    /**
     * Handle the registration request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'company_name' => 'required',
            'domain' => 'required|alpha_dash|unique:companies,domain|not_in:www,admin',
            'name' => 'required',
            'email' => 'required|email', // We should check email uniqueness properly later, maybe scoped
            'password' => 'required|confirmed|min:6',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create Company
            $company = $this->companyRepository->create([
                'name' => request('company_name'),
                'domain' => request('domain'),
                'status' => 1,
            ]);

            // 2. Create Admin User
            $user = new User([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'status' => 1,
                'role_id' => 1, // Admin Role
                'is_superuser' => 0,
            ]);
            $user->company_id = $company->id;
            $user->save();

            DB::commit();

            // 3. Dispatch Events
            Event::dispatch('saas.company.registered', $company);

            // 4. Redirect to the new domain
            $protocol = request()->secure() ? 'https://' : 'http://';
            $domain = request('domain');
            $appDomain = config('app.url'); // This might need parsing if it contains protocol

            // For now, assume a simple redirect to the index page of the new tenant
            // In a real env, we'd construct the full URL: http://subdomain.domain.com/admin/login

            // We'll flash a message that will likely be lost if we change domains, 
            // but if we redirect to a success page on the MAIN domain first, it works.

            return redirect()->route('saas.register.success', ['domain' => $domain]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Registration failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function success()
    {
        $domain = request('domain');
        // Construct target URL
        // Currently assuming localhost or similar structure. 
        // Ideally we grab the root domain from config.
        $host = request()->getHost();
        $targetUrl = request()->schemeAndHttpHost();

        // This logic depends heavily on how the main domain is accessed.
        // For MVP, we'll just show the link.

        return view('saas::registration.success', compact('domain'));
    }
}
