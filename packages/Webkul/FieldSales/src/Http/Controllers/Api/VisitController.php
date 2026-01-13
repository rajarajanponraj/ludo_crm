<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Visit;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    /**
     * Check In (Start Visit).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $user = auth()->user();

            // Check if there is an ongoing visit? 
            $ongoingVisit = Visit::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->first();

            if ($ongoingVisit) {
                return response()->json(['message' => 'You have an ongoing visit. Please complete it first.'], 400);
            }

            // Geo-Fencing Logic
            $person = \Webkul\Contact\Models\Person::find($request->person_id);

            if ($person->latitude && $person->longitude) {
                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $person->latitude,
                    $person->longitude
                );

                // 200 meters allowed radius
                if ($distance > 0.2) {
                    return response()->json([
                        'message' => 'You are too far from the customer location.',
                        'distance_km' => round($distance, 3)
                    ], 400);
                }
            } else {
                // First visit sets the location (Self-learning)
                $person->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ]);
            }

            $visit = Visit::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'person_id' => $request->person_id,
                'check_in_at' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'in_progress',
            ]);

            return response()->json([
                'message' => 'Visit started successfully.',
                'data' => $visit,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Visit Start Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to start visit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate distance between two points in km (Haversine formula).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check Out (End Visit).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'feedback' => 'nullable|string',
            'images' => 'nullable|array',
        ]);

        try {
            $user = auth()->user();

            $visit = Visit::where('id', $request->visit_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$visit) {
                return response()->json(['message' => 'Visit not found.'], 404);
            }

            if ($visit->status === 'completed') {
                return response()->json(['message' => 'Visit already completed.'], 400);
            }

            $visit->update([
                'check_out_at' => now(),
                'status' => 'completed',
                'feedback' => $request->feedback,
                'images' => $request->images,
                // Update location if provided at end
                'latitude' => $request->latitude ?? $visit->latitude,
                'longitude' => $request->longitude ?? $visit->longitude,
            ]);

            return response()->json([
                'message' => 'Visit completed successfully.',
                'data' => $visit,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Visit End Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to complete visit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
