<x-admin::layouts>
    <x-slot:title>
        Create Route Plan
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">New Route Plan</h1>
    </div>

    <form action="{{ route('field_sales.admin.routes.store') }}" method="POST"
        class="bg-white rounded-lg shadow p-6 max-w-2xl">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Route Name (Optional)</label>
            <input type="text" name="name"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="e.g. Downtown Area">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Agent</label>
            <select name="user_id" class="w-full border rounded px-3 py-2 bg-white" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Customers to Visit</label>
            <div class="border rounded max-h-64 overflow-y-auto p-2 space-y-2">
                @foreach($persons as $person)
                    <div class="flex items-center">
                        <input type="checkbox" name="persons[]" value="{{ $person->id }}" id="person_{{ $person->id }}"
                            class="mr-2 h-4 w-4">
                        <label for="person_{{ $person->id }}" class="text-sm text-gray-800">
                            {{ $person->name }}
                            <span class="text-gray-500 text-xs">({{ $person->organization->name ?? 'No Org' }})</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-1">Select at least one customer.</p>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('field_sales.admin.routes.index') }}"
                class="mr-4 px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Route</button>
        </div>
    </form>
</x-admin::layouts>