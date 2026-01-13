<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Attendance;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Check In.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'nullable|date',
        ]);

        try {
            $user = auth()->user();
            $today = Carbon::today()->toDateString();

            // Check if already checked in
            $existing = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->where('company_id', $user->company_id)
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Already checked in for today.'], 400);
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'date' => $today,
                'check_in' => $request->timestamp ? Carbon::parse($request->timestamp) : now(),
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'ip_address' => $request->ip(),
                'distance_travelled' => 0,
            ]);

            return response()->json([
                'message' => 'Checked in successfully.',
                'data' => $attendance,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Attendance Check-In Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to check in.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'nullable|date',
        ]);

        try {
            $user = auth()->user();
            $today = Carbon::today()->toDateString();

            // Find active attendance for today
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->where('company_id', $user->company_id)
                ->first();

            if (!$attendance) {
                return response()->json(['message' => 'No check-in record found for today.'], 404);
            }

            if ($attendance->check_out) {
                return response()->json(['message' => 'Already checked out for today.'], 400);
            }

            $checkOutTime = $request->timestamp ? Carbon::parse($request->timestamp) : now();

            $attendance->update([
                'check_out' => $checkOutTime,
                'check_out_lat' => $request->latitude,
                'check_out_lng' => $request->longitude,
            ]);

            // TODO: Calculate distance based on user_locations or straight line distance

            return response()->json([
                'message' => 'Checked out successfully.',
                'data' => $attendance,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Attendance Check-Out Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to check out.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
