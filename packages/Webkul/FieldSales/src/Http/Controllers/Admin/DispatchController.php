<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\FieldSales\Models\Order;
use Webkul\User\Models\User;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of pending/processing orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with(['user', 'person', 'dispatcher'])
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $dispatchers = User::all(); // In a real app, filter by role 'dispatcher'

        return view('field_sales::dispatch.index', compact('orders', 'dispatchers'));
    }

    /**
     * Assign a Dispatcher to an Order.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign($id)
    {
        $this->validate(request(), [
            'dispatcher_id' => 'required|exists:users,id',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'dispatcher_id' => request('dispatcher_id'),
            'status' => 'approved', // Move to approved once assigned
        ]);

        session()->flash('success', 'Dispatcher assigned successfully.');

        return redirect()->back();
    }

    /**
     * Mark Order as Dispatched.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dispatchOrder($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->dispatcher_id) {
            session()->flash('error', 'Please assign a dispatcher first.');
            return redirect()->back();
        }

        $order->update([
            'status' => 'dispatched',
        ]);

        // TODO: Send Notification to Sales Agent and Customer

        session()->flash('success', 'Order marked as dispatched.');

        return redirect()->back();
    }
}
