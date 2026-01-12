<?php

namespace Webkul\SAAS\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\SAAS\Repositories\CompanyRepository;
use Webkul\SAAS\Http\Requests\CompanyRequest;

class CompanyController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('saas::companies.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('saas::companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(): \Illuminate\Http\RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required',
            'domain' => 'required|unique:companies,domain',
            'status' => 'boolean',
        ]);

        Event::dispatch('saas.company.create.before');

        $company = $this->companyRepository->create(request()->all());

        // Create Default Admin User
        $user = new \Webkul\User\Models\User([
            'name' => 'Admin',
            'email' => 'admin@' . request('domain') . '.com',
            'password' => bcrypt('admin123'),
            'status' => 1,
            'role_id' => 1, // Assuming 1 is Admin
            'is_superuser' => 0,
        ]);
        $user->company_id = $company->id;
        $user->save();

        Event::dispatch('saas.company.create.after', $company);

        session()->flash('success', trans('admin::app.settings.companies.create-success') . ' Default Admin: admin@' . request('domain') . '.com / admin123');

        return redirect()->route('saas.companies.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $company = $this->companyRepository->findOrFail($id);

        return view('saas::companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id): \Illuminate\Http\RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required',
            'domain' => 'required|unique:companies,domain,' . $id,
            'status' => 'boolean',
        ]);

        Event::dispatch('saas.company.update.before', $id);

        $company = $this->companyRepository->update(request()->all(), $id);

        Event::dispatch('saas.company.update.after', $company);

        session()->flash('success', trans('admin::app.settings.companies.update-success'));

        return redirect()->route('saas.companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $this->companyRepository->delete($id);

        session()->flash('success', trans('admin::app.settings.companies.delete-success'));

        return response()->json(['message' => false], 400);
    }
}
