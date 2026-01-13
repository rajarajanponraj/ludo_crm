<x-admin::layouts>
    <x-slot:title>
        Field Sales Analytics
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Field Sales Analytics</h1>
        <span class="text-sm text-gray-500">Overview for {{ date('F Y') }}</span>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm font-medium uppercase">Pending Dispatches</div>
            <div class="mt-2 text-3xl font-bold text-gray-800">{{ $pendingDispatches }}</div>
            <div class="text-xs text-gray-400 mt-1">Orders waiting to be shipped</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm font-medium uppercase">Today's Sales</div>
            <div class="mt-2 text-3xl font-bold text-gray-800">{{ core()->formatBasePrice($todaySales) }}</div>
            <div class="text-xs text-gray-400 mt-1">Total order value generated today</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm font-medium uppercase">Today's Collections</div>
            <div class="mt-2 text-3xl font-bold text-gray-800">{{ core()->formatBasePrice($todayCollections) }}</div>
            <div class="text-xs text-gray-400 mt-1">Total payments collected today</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Performing Agents -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">
                Top Agents (This Month)
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Orders</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topAgents as $agent)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $agent->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ $agent->total_orders }}</td>
                            <td class="px-6 py-4 text-sm text-green-600 font-bold text-right">
                                {{ core()->formatBasePrice($agent->total_sales) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">No sales data recorded this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">
                Recent Orders
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Amount</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $order->person->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-right">
                                {{ core()->formatBasePrice($order->grand_total) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Target vs Actuals -->
    <div class="bg-white rounded-lg shadow overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">
            Target Achievement (Current Period)
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Agent</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Target Type</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Target</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Actual</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Achievement %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($targets as $target)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $target->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $target->type)) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">
                            {{ $target->type == 'sales_amount' ? core()->formatBasePrice($target->target_value) : (int) $target->target_value }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            {{ $target->type == 'sales_amount' ? core()->formatBasePrice($target->actual) : $target->actual }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end">
                                <span
                                    class="text-sm font-bold {{ $target->achievement_percent >= 100 ? 'text-green-600' : ($target->achievement_percent >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($target->achievement_percent, 1) }}%
                                </span>
                                <div class="w-24 bg-gray-200 rounded-full h-2.5 ml-2">
                                    <div class="h-2.5 rounded-full {{ $target->achievement_percent >= 100 ? 'bg-green-600' : ($target->achievement_percent >= 70 ? 'bg-yellow-400' : 'bg-red-500') }}"
                                        style="width: {{ min($target->achievement_percent, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No active targets found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin::layouts>