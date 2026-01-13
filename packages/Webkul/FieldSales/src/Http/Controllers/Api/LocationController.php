<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\UserLocation;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Store user location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'battery_level' => 'nullable|integer|between:0,100',
            'timestamp' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $location = UserLocation::create([
                'user_id' => $user->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'battery_level' => $request->battery_level,
                'address' => $request->address,
                'tracked_at' => $request->timestamp ?? now(),
            ]);

            return response()->json([
                'message' => 'Location stored successfully.',
                'data' => $location,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Location Update Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to store location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
