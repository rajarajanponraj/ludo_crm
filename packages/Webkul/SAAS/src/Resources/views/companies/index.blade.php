<x-admin::layouts>
    <x-slot:title>
        {{ __('admin::app.settings.companies.title') }}
        </x-slot>

        <div class="content full-page">
            <table-component data-src="{{ route('saas.companies.index') }}">
                <template v-slot:table-header>
                    <h1>
                        {{ __('admin::app.settings.companies.title') }}
                    </h1>
                </template>

                <template v-slot:table-action>
                    <a href="{{ route('saas.companies.create') }}" class="btn btn-md btn-primary">
                        {{ __('admin::app.settings.companies.create') }}
                    </a>
                </template>
            </table-component>
        </div>
</x-admin::layouts>