<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Product\Models\Product;
use Webkul\Contact\Models\Person;
use Webkul\FieldSales\Models\Order;
use Webkul\FieldSales\Models\Visit;
use Carbon\Carbon;

class DataController extends Controller
{
    /**
     * Sync Data (Differential Sync).
     * Mobile app sends ?last_synced_at=TIMESTAMP
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        $user = auth()->user();
        $lastSyncedAt = $request->input('last_synced_at');

        // Define query scope based on timestamp
        $applySync = function ($query) use ($lastSyncedAt) {
            if ($lastSyncedAt) {
                $query->where('updated_at', '>=', Carbon::parse($lastSyncedAt));
            }
        };

        // 1. Products (Catalog)
        $products = Product::where('company_id', $user->company_id)
            ->where(function ($q) use ($applySync) {
                $applySync($q);
            })
            ->get();

        // 2. Customers (Persons)
        $customers = Person::where('company_id', $user->company_id)
            ->where(function ($q) use ($applySync) {
                $applySync($q);
            })
            ->get();

        // 3. My Orders (Updates)
        $orders = Order::where('user_id', $user->id)
            ->where(function ($q) use ($applySync) {
                $applySync($q);
            })
            ->get();

        $deletedOrders = [];
        if ($lastSyncedAt) {
            $deletedOrders = Order::withoutGlobalScopes()->onlyTrashed()
                ->where('user_id', $user->id)
                ->where('deleted_at', '>=', Carbon::parse($lastSyncedAt))
                ->pluck('id');
        }

        // 4. My Visits (Updates)
        $visits = Visit::where('user_id', $user->id)
            ->where(function ($q) use ($applySync) {
                $applySync($q);
            })
            ->get();

        $deletedVisits = [];
        if ($lastSyncedAt) {
            $deletedVisits = Visit::withoutGlobalScopes()->onlyTrashed()
                ->where('user_id', $user->id)
                ->where('deleted_at', '>=', Carbon::parse($lastSyncedAt))
                ->pluck('id');
        }

        return response()->json([
            'message' => 'Sync data fetched successfully.',
            'server_timestamp' => now()->toIso8601String(),
            'data' => [
                'products' => $products,
                'customers' => $customers,
                'orders' => $orders,
                'visits' => $visits,
                'deleted' => [
                    'orders' => $deletedOrders,
                    'visits' => $deletedVisits,
                ],
            ]
        ]);
    }
}
