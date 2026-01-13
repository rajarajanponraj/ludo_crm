<?php

namespace Webkul\FieldSales\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Webkul\FieldSales\Models\Order;
use Webkul\FieldSales\Models\Collection;
use Webkul\User\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display Reporting Dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Pending Dispatches
        $pendingDispatches = Order::where('status', 'pending')
            ->orWhere('status', 'approved')
            ->count();

        // 2. Today's Sales
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->sum('grand_total');

        // 3. Today's Collections
        $todayCollections = Collection::whereDate('created_at', Carbon::today())
            ->sum('amount');

        // 4. Sales Agent Performance (Top 5 by Order Value this Month)
        $topAgents = Order::select('user_id', DB::raw('SUM(grand_total) as total_sales'), DB::raw('COUNT(id) as total_orders'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // 5. Recent Orders
        $recentOrders = Order::with(['user', 'person'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Target vs Actuals (Current Month)
        $targets = \Webkul\FieldSales\Models\Target::where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->with('user')
            ->get()
            ->map(function ($target) {
                $actual = 0;
                if ($target->type == 'sales_amount') {
                    $actual = Order::where('user_id', $target->user_id)
                        ->whereBetween('created_at', [$target->start_date, $target->end_date])
                        ->sum('grand_total');
                } elseif ($target->type == 'visit_count') {
                    $actual = \Webkul\FieldSales\Models\Visit::where('user_id', $target->user_id)
                        ->whereBetween('check_in_at', [$target->start_date, $target->end_date])
                        ->count();
                }

                $target->actual = $actual;
                $target->achievement_percent = $target->target_value > 0 ? ($actual / $target->target_value) * 100 : 0;
                return $target;
            });

        return view('field_sales::reports.index', compact(
            'pendingDispatches',
            'todaySales',
            'todayCollections',
            'topAgents',
            'recentOrders',
            'targets'
        ));
    }
}
