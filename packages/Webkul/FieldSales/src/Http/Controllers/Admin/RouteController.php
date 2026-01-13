<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\FieldSales\Models\Route;
use Webkul\FieldSales\Models\RouteItem;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $routes = Route::with(['user', 'items'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('field_sales::routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::all();
        // In a real app, you might filter persons by ownership or location
        $persons = Person::all();

        return view('field_sales::routes.create', compact('users', 'persons'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'persons' => 'required|array',
            'persons.*' => 'exists:persons,id',
        ]);

        DB::beginTransaction();

        try {
            $route = Route::create([
                'name' => request('name'),
                'user_id' => request('user_id'),
                'date' => request('date'),
                'status' => 'active',
            ]);

            foreach (request('persons') as $personId) {
                RouteItem::create([
                    'field_route_id' => $route->id,
                    'person_id' => $personId,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            session()->flash('success', 'Route created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }

        return redirect()->route('field_sales.admin.routes.index');
    }
}
