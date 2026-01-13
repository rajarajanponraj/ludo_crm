<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Collection;
use Illuminate\Support\Facades\Log;

class CollectionController extends Controller
{
    /**
     * List Collections history.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $collections = Collection::where('user_id', $user->id)
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Collections fetched successfully.',
            'data' => $collections
        ]);
    }

    /**
     * Store Collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:cash,check,online_transfer',
            'transaction_id' => 'nullable|string',
            'proof_image' => 'nullable|image|max:2048', // Allow image upload
        ]);

        try {
            $user = auth()->user();

            $data = [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'person_id' => $request->person_id,
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'payment_mode' => $request->payment_mode,
                'transaction_id' => $request->transaction_id,
                'collected_at' => now(),
            ];

            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('collections', 'public');
                $data['proof_image'] = $path;
            }

            $collection = Collection::create($data);

            return response()->json([
                'message' => 'Payment collected successfully.',
                'data' => $collection,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Collection Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to collect payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
