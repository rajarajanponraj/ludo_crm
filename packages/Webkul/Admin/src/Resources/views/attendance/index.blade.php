<x-admin::layouts>
    <x-slot:title>
        Attendance
        </x-slot>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daily Attendance</h1>
            <div class="text-gray-500">{{ \Carbon\Carbon::now()->format('l, jS F Y') }}</div>
        </div>

        <div class="grid gap-6">
            <!-- Action Card -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 text-center">
                @if (!$todayAttendance)
                    <div class="mb-4">
                        <div class="text-gray-500 mb-2">You have not checked in today.</div>
                        <form action="{{ route('admin.attendance.store') }}" method="POST" id="checkInForm">
                            @csrf
                            <input type="hidden" name="lat" id="in_lat">
                            <input type="hidden" name="lng" id="in_lng">
                            <button type="button" onclick="getLocation('checkInForm', 'in_lat', 'in_lng')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                                Check In
                            </button>
                        </form>
                    </div>
                @elseif (!$todayAttendance->check_out)
                    <div class="mb-4">
                        <div class="text-green-600 font-medium mb-2">
                            Checked In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}
                        </div>
                        <form action="{{ route('admin.attendance.update', $todayAttendance->id) }}" method="POST"
                            id="checkOutForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="lat" id="out_lat">
                            <input type="hidden" name="lng" id="out_lng">
                            <button type="button" onclick="getLocation('checkOutForm', 'out_lat', 'out_lng')"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                                Check Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-gray-600">
                        <div class="mb-1">Checked In: <span
                                class="font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</span>
                        </div>
                        <div>Checked Out: <span
                                class="font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</span>
                        </div>
                        <div class="mt-4 text-green-500 font-bold">You are done for today!</div>
                    </div>
                @endif
                <div id="locationStatus" class="mt-2 text-sm text-gray-400"></div>
            </div>

            <!-- History Table -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 font-bold">
                    History (Last 30 Days)
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Check In</th>
                                <th class="px-6 py-3">Check Out</th>
                                <th class="px-6 py-3">Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $record)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4">{{ $record->date }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($record->check_in_lat)
                                            <a href="https://www.google.com/maps?q={{ $record->check_in_lat }},{{ $record->check_in_lng }}"
                                                target="_blank" class="text-blue-600 hover:underline">
                                                View Map
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function getLocation(formId, latId, lngId) {
                const status = document.getElementById('locationStatus');
                status.textContent = "Locating...";

                if (!navigator.geolocation) {
                    status.textContent = "Geolocation is not supported by your browser";
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById(latId).value = position.coords.latitude;
                        document.getElementById(lngId).value = position.coords.longitude;
                        status.textContent = "";
                        document.getElementById(formId).submit();
                    },
                    (error) => {
                        status.textContent = "Unable to retrieve your location. Please allow location access.";
                        console.error(error);
                    }
                );
            }
        </script>
</x-admin::layouts>