<x-admin::layouts>
    <x-slot:title>
        Import / Export Data
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Import / Export Data</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Export Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold mb-4">Export Orders</h2>
            <p class="text-gray-600 mb-4">Download a CSV file containing all field orders.</p>
            <a href="{{ route('field_sales.admin.export.orders') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Download CSV
            </a>
        </div>

        <!-- Import Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold mb-4">Import Products</h2>
            <p class="text-gray-600 mb-4">Upload a CSV file to update or add products.</p>
            <p class="text-sm text-gray-400 mb-4">Format: Name, SKU, Price, Description, Quantity</p>

            <form action="{{ route('field_sales.admin.import.products') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="file" accept=".csv" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                    " />
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Upload & Import
                </button>
            </form>
        </div>
    </div>
</x-admin::layouts>