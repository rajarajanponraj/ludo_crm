<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Route;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RouteController extends Controller
{
    /**
     * Get My Route for Today.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $date = $request->date ?? Carbon::today()->toDateString();

            $route = Route::with(['items.person', 'items.person.organization'])
                ->where('user_id', $user->id)
                ->where('date', $date)
                ->where('status', 'active')
                ->first();

            if (!$route) {
                return response()->json(['message' => 'No active route found for this date.'], 404);
            }

            return response()->json([
                'message' => 'Route fetched successfully.',
                'data' => $route,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Route Fetch Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch route.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
