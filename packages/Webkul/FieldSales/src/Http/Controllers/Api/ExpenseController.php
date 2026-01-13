<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Expense;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    /**
     * List Expenses history.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $expenses = Expense::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Expenses fetched successfully.',
            'data' => $expenses
        ]);
    }

    /**
     * Submit Expense Claim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'attachment_path' => 'nullable|image|max:2048',
        ]);

        try {
            $user = auth()->user();

            $data = [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'pending',
            ];

            if ($request->hasFile('attachment_path')) {
                $path = $request->file('attachment_path')->store('expenses', 'public');
                $data['attachment_path'] = $path;
            }

            $expense = Expense::create($data);

            return response()->json([
                'message' => 'Expense submitted successfully.',
                'data' => $expense,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Expense Submission Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to submit expense.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
