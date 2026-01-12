<x-admin::layouts>
    <x-slot:title>
        {{ __('admin::app.settings.companies.create-title') }}
        </x-slot>

        <div class="content full-page adjacent-center">
            {!! view_render_event('saas.companies.create.header.before') !!}

            <div class="page-header">
                <div class="page-title">
                    <h1>{{ __('admin::app.settings.companies.create-title') }}</h1>
                </div>
            </div>

            {!! view_render_event('saas.companies.create.header.after') !!}

            <form method="POST" action="{{ route('saas.companies.store') }}" @submit.prevent="onSubmit">
                <div class="page-content">
                    <div class="form-container">
                        <div class="panel">
                            <div class="panel-header">
                                {!! view_render_event('saas.companies.create.form_controls.before') !!}

                                <button type="submit" class="btn btn-md btn-primary">
                                    {{ __('admin::app.settings.companies.save-btn-title') }}
                                </button>

                                <a href="{{ route('saas.companies.index') }}">
                                    {{ __('admin::app.settings.companies.back') }}
                                </a>

                                {!! view_render_event('saas.companies.create.form_controls.after') !!}
                            </div>

                            <div class="panel-body">
                                {!! view_render_event('saas.companies.create.form_fields.before') !!}

                                @csrf

                                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                    <label for="name"
                                        class="required">{{ __('admin::app.settings.companies.name') }}</label>
                                    <input type="text" v-validate="'required'" class="form-control" id="name"
                                        name="name" value="{{ old('name') }}"
                                        data-vv-as="&quot;{{ __('admin::app.settings.companies.name') }}&quot;" />
                                    <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name')
                                        }}</span>
                                </div>

                                <div class="form-group" :class="[errors.has('domain') ? 'has-error' : '']">
                                    <label for="domain"
                                        class="required">{{ __('admin::app.settings.companies.domain') }}</label>
                                    <input type="text" v-validate="'required'" class="form-control" id="domain"
                                        name="domain" value="{{ old('domain') }}"
                                        data-vv-as="&quot;{{ __('admin::app.settings.companies.domain') }}&quot;" />
                                    <span class="control-error" v-if="errors.has('domain')">@{{ errors.first('domain')
                                        }}</span>
                                    <small class="form-text text-muted">Subdomain for the tenant (e.g. 'alpha' for
                                        alpha.crm.com)</small>
                                </div>

                                <div class="form-group">
                                    <label for="status">{{ __('admin::app.settings.companies.status') }}</label>
                                    <label class="switch">
                                        <input type="checkbox" id="status" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                {!! view_render_event('saas.companies.create.form_fields.after') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</x-admin::layouts>