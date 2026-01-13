<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Leave;

class LeaveController extends Controller
{
    /**
     * List user's leaves.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $leaves = Leave::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => $leaves,
            'message' => 'Leaves fetched successfully.'
        ]);
    }

    /**
     * Apply for leave.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        $leave = Leave::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Leave applied successfully.',
            'data' => $leave
        ], 201);
    }
}
