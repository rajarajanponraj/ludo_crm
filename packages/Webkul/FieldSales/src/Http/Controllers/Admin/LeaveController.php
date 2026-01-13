<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\FieldSales\Models\Leave;

class LeaveController extends Controller
{
    /**
     * Display a listing of leaves.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $leaves = Leave::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('field_sales::leaves.index', compact('leaves'));
    }

    /**
     * Update leave status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $leave = Leave::findOrFail($id);

        $leave->update([
            'status' => $request->status,
            'approved_by' => auth()->guard('user')->id(),
        ]);

        session()->flash('success', 'Leave status updated successfully.');

        return redirect()->back();
    }
}
