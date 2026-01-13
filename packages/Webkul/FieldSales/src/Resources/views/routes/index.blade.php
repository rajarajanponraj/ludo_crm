<x-admin::layouts>
    <x-slot:title>
        Route Plans
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Route Plans</h1>
        <a href="{{ route('field_sales.admin.routes.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create Route
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Agent</th>
                    <th class="p-4 font-semibold">Name</th>
                    <th class="p-4 font-semibold">Visits</th>
                    <th class="p-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routes as $route)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">{{ $route->date->format('Y-m-d') }}</td>
                        <td class="p-4">{{ $route->user->name }}</td>
                        <td class="p-4">{{ $route->name ?? '-' }}</td>
                        <td class="p-4">{{ $route->items->count() }} Customers</td>
                        <td class="p-4">
                            <span
                                class="px-2 py-1 text-xs rounded {{ $route->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($route->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach

                @if($routes->isEmpty())
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            No route plans found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="p-4">
            {{ $routes->links() }}
        </div>
    </div>
</x-admin::layouts>