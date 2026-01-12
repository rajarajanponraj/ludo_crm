<x-admin::layouts.anonymous>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white">
                Register Your Company
            </h2>

            <!-- Session Status -->
            <x-admin::form.control-group.error control-name="error" />

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                    role="alert">
                    <span class="font-medium">Error!</span> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('saas.register.store') }}">
                @csrf

                <!-- Company Name -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Company Name
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="text" name="company_name"
                            :value="old('company_name')" required autofocus placeholder="My Great Company" />

                        <x-admin::form.control-group.error control-name="company_name" />
                    </x-admin::form.control-group>
                </div>

                <!-- Domain -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Desired Subdomain
                        </x-admin::form.control-group.label>

                        <div class="flex">
                            <x-admin::form.control-group.control type="text" name="domain" :value="old('domain')"
                                required placeholder="company" class="rounded-r-none" />
                            <span
                                class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                .{{ request()->getHost() }}
                            </span>
                        </div>

                        <x-admin::form.control-group.error control-name="domain" />
                    </x-admin::form.control-group>
                </div>

                <!-- Admin Name -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Your Name
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="text" name="name" :value="old('name')" required
                            placeholder="John Doe" />

                        <x-admin::form.control-group.error control-name="name" />
                    </x-admin::form.control-group>
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Email Address
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="email" name="email" :value="old('email')" required
                            placeholder="admin@company.com" />

                        <x-admin::form.control-group.error control-name="email" />
                    </x-admin::form.control-group>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Password
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="password" name="password" required />

                        <x-admin::form.control-group.error control-name="password" />
                    </x-admin::form.control-group>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            Confirm Password
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="password" name="password_confirmation" required />
                    </x-admin::form.control-group>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="text-sm text-gray-600 underline hover:text-gray-900"
                        href="{{ route('admin.session.create') }}">
                        Already registered?
                    </a>

                    <button type="submit" class="ml-4 btn btn-primary">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts.anonymous>