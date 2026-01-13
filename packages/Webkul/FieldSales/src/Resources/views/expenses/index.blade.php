<x-admin::layouts>
    <x-slot:title>
        Expense Approvals
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Expense Approvals</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Agent</th>
                    <th class="p-4 font-semibold">Type</th>
                    <th class="p-4 font-semibold">Amount</th>
                    <th class="p-4 font-semibold">Description</th>
                    <th class="p-4 font-semibold">Attachment</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-4">{{ $expense->created_at->format('Y-m-d') }}</td>
                                <td class="p-4">{{ $expense->user->name }}</td>
                                <td class="p-4">{{ $expense->type }}</td>
                                <td class="p-4 font-bold">{{ core()->formatBasePrice($expense->amount) }}</td>
                                <td class="p-4 text-sm">{{ Str::limit($expense->description, 30) }}</td>
                                <td class="p-4">
                                    @if($expense->attachment_path)
                                        <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank"
                                            class="text-blue-600 underline text-xs">View</a>
                                    @else
                                        <span class="text-gray-400 text-xs">No File</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded 
                                            {{ $expense->status == 'approved' ? 'bg-green-100 text-green-800' :
                    ($expense->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($expense->status) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($expense->status == 'pending')
                                        <div class="flex space-x-2">
                                            <form action="{{ route('field_sales.admin.expenses.approve', $expense->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">Approve</button>
                                            </form>
                                            <form action="{{ route('field_sales.admin.expenses.reject', $expense->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Reject</button>
                                            </form>
                                        </div>
                                    @elseif($expense->status == 'approved')
                                        <span class="text-green-600 text-xs">By {{ $expense->approver->name ?? 'Admin' }}</span>
                                    @endif
                                </td>
                            </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $expenses->links() }}
        </div>
    </div>
</x-admin::layouts>