<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\FieldSales\Models\Expense;

class ExpenseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * List Expenses for Approval.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $expenses = Expense::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('field_sales::expenses.index', compact('expenses'));
    }

    /**
     * Approve Expense.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $expense = Expense::findOrFail($id);

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Expense approved successfully.');

        return redirect()->back();
    }

    /**
     * Reject Expense.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $expense = Expense::findOrFail($id);

        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Expense rejected.');

        return redirect()->back();
    }
}
