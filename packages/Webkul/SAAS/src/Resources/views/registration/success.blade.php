<x-admin::layouts.anonymous>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md dark:bg-gray-800 text-center">
            <h2 class="text-3xl font-bold text-green-600">
                Success!
            </h2>

            <p class="text-gray-600 dark:text-gray-300">
                Your workspace <strong class="text-blue-500">{{ $domain }}</strong> has been created successfully.
            </p>

            <div class="mt-6">
                @php
                    // Dynamically constructing the URL. 
                    // This assumes standard subdomain routing.
                    $currentHost = request()->getHost();
                    // If current host is 'localhost' or '127.0.0.1', we append domain.
                    // If it's real domain like 'app.com', we prepend.
                    // This is a rough estimation for the view.

                    $protocol = request()->secure() ? 'https://' : 'http://';
                    // Strip the current subdomain if any? 
                    // For local dev with 'localhost', we usually need 'domain.localhost'.

                    // Simple logic for MVP:
                    $url = $protocol . $domain . '.' . $currentHost . '/admin/login';
                    if (str_contains($currentHost, 'localhost')) {
                        $url = $protocol . $domain . '.localhost/admin/login';
                    }
                @endphp

                <a href="{{ $url }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Go to Your Dashboard
                </a>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                You can login with the email and password you just provided.
            </p>
        </div>
    </div>
</x-admin::layouts.anonymous>