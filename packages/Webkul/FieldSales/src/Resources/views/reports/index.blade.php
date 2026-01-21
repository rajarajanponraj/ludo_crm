<x-admin::layouts>
    <x-slot:title>
        Field Sales Analytics
    </x-slot:title>

    <div class="flex flex-col gap-8">
        <!-- Header -->
        <div class="flex justify-between items-end border-b pb-6 border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Field Sales Analytics</h1>
                <p class="text-gray-500 mt-2">Overview for <span
                        class="font-medium text-gray-800">{{ date('F Y') }}</span></p>
            </div>

            <div class="flex gap-2">
                <!-- Potential Date Filter / Action Buttons could go here -->
                <button
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Export Report
                </button>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pending Dispatches -->
            <div
                class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Pending Dispatches</h3>
                    </div>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-gray-900">{{ $pendingDispatches }}</span>
                        <span class="ml-2 text-sm text-gray-500 font-medium">Orders</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Waiting to be shipped</p>
                </div>
            </div>

            <!-- Today's Sales -->
            <div
                class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-green-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Today's Sales</h3>
                    </div>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-gray-900">{{ core()->formatBasePrice($todaySales) }}</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Total value generated today</p>
                </div>
            </div>

            <!-- Today's Collections -->
            <div
                class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-purple-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Today's Collections
                        </h3>
                    </div>
                    <div class="mt-4">
                        <span
                            class="text-4xl font-bold text-gray-900">{{ core()->formatBasePrice($todayCollections) }}</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Total payments collected</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Top Performing Agents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-lg">Top Agents</h3>
                    <span
                        class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full uppercase tracking-wide">This
                        Month</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Agent</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Orders</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Sales Volume
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($topAgents as $index => $agent)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3">
                                                {{ substr($agent->user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $agent->user->name }}</div>
                                                @if($index == 0)
                                                    <div class="text-xs text-yellow-600 flex items-center mt-0.5">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                        Top Performer
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-600 font-medium text-sm">{{ $agent->total_orders }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ core()->formatBasePrice($agent->total_sales) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            </svg>
                                            <p>No sales data recorded this month.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-lg">Recent Orders</h3>
                    <a href="{{ route('field-sales.api.orders.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Amount</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                        #{{ $order->id }}
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $order->person->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 text-right font-medium">
                                        {{ core()->formatBasePrice($order->grand_total) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'approved' => 'bg-green-50 text-green-700 border-green-200',
                                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                                'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            ];
                                            $currentClass = $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $currentClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            <p>No recent orders found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Target vs Actuals -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Target Achievement <span
                        class="text-gray-400 font-normal text-sm ml-2">(Current Period)</span></h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider w-1/4">Agent</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider w-1/6">Target Type</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right w-1/6">Target
                            </th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right w-1/6">Actual
                            </th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-left w-1/4 pl-10">
                                Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($targets as $target)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $target->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 uppercase text-xs font-bold tracking-wide">
                                    <span
                                        class="bg-gray-100 px-2 py-1 rounded">{{ str_replace('_', ' ', $target->type) }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right font-medium">
                                    {{ $target->type == 'sales_amount' ? core()->formatBasePrice($target->target_value) : (int) $target->target_value }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                    {{ $target->type == 'sales_amount' ? core()->formatBasePrice($target->actual) : $target->actual }}
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner w-32">
                                            <div class="h-full rounded-full transition-all duration-500 ease-out {{ $target->achievement_percent >= 100 ? 'bg-gradient-to-r from-green-400 to-green-600' : ($target->achievement_percent >= 70 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-red-400 to-red-600') }}"
                                                style="width: {{ min($target->achievement_percent, 100) }}%"></div>
                                        </div>
                                        <span
                                            class="text-xs font-bold w-12 text-right {{ $target->achievement_percent >= 100 ? 'text-green-600' : ($target->achievement_percent >= 70 ? 'text-yellow-600' : 'text-gray-600') }}">
                                            {{ number_format($target->achievement_percent, 1) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <p>No active targets found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin::layouts>