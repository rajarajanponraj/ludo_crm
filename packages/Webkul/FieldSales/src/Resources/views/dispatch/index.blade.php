<x-admin::layouts>
    <x-slot:title>
        Dispatch Center
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Dispatch Center</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-4 font-semibold">Order ID</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Customer</th>
                    <th class="p-4 font-semibold">Agent</th>
                    <th class="p-4 font-semibold">Amount</th>
                    <th class="p-4 font-semibold">Dispatcher</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">#{{ $order->id }}</td>
                        <td class="p-4">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-4">
                            {{ $order->person->name }}<br>
                            <span class="text-xs text-gray-500">{{ $order->type }}</span>
                        </td>
                        <td class="p-4">{{ $order->user->name }}</td>
                        <td class="p-4 font-bold">{{ core()->formatBasePrice($order->grand_total) }}</td>
                        <td class="p-4">
                            @if($order->dispatcher)
                                {{ $order->dispatcher->name }}
                            @else
                                <span class="text-red-500 text-xs">Unassigned</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span
                                class="px-2 py-1 text-xs rounded {{ $order->status == 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex space-x-2">
                                <!-- Assign Dispatcher Modal/Form -->
                                <form action="{{ route('field_sales.admin.dispatch.assign', $order->id) }}" method="POST"
                                    class="flex items-center space-x-1">
                                    @csrf
                                    <select name="dispatcher_id" class="text-xs border rounded p-1" required>
                                        <option value="">Select Dispatcher</option>
                                        @foreach($dispatchers as $d)
                                            <option value="{{ $d->id }}" {{ $order->dispatcher_id == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs hover:bg-gray-300">Assign</button>
                                </form>

                                <!-- Dispatch Button -->
                                @if($order->dispatcher_id)
                                    <form action="{{ route('field_sales.admin.dispatch.dispatch', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">Dispatch</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if($orders->isEmpty())
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            No pending orders.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="p-4">
            {{ $orders->links() }}
        </div>
    </div>
</x-admin::layouts>