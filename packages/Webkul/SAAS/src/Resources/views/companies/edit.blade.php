<x-admin::layouts>
    <x-slot:title>
        {{ __('admin::app.settings.companies.edit-title') }}
        </x-slot>

        <div class="content full-page adjacent-center">
            {!! view_render_event('saas.companies.edit.header.before', ['company' => $company]) !!}

            <div class="page-header">
                <div class="page-title">
                    <h1>{{ __('admin::app.settings.companies.edit-title') }}</h1>
                </div>
            </div>

            {!! view_render_event('saas.companies.edit.header.after', ['company' => $company]) !!}

            <form method="POST" action="{{ route('saas.companies.update', $company->id) }}" @submit.prevent="onSubmit">
                <div class="page-content">
                    <div class="form-container">
                        <div class="panel">
                            <div class="panel-header">
                                {!! view_render_event('saas.companies.edit.form_controls.before', ['company' => $company]) !!}

                                <button type="submit" class="btn btn-md btn-primary">
                                    {{ __('admin::app.settings.companies.save-btn-title') }}
                                </button>

                                <a href="{{ route('saas.companies.index') }}">
                                    {{ __('admin::app.settings.companies.back') }}
                                </a>

                                {!! view_render_event('saas.companies.edit.form_controls.after', ['company' => $company]) !!}
                            </div>

                            <div class="panel-body">
                                {!! view_render_event('saas.companies.edit.form_fields.before', ['company' => $company]) !!}

                                @csrf
                                @method('PUT')

                                <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                                    <label for="name"
                                        class="required">{{ __('admin::app.settings.companies.name') }}</label>
                                    <input type="text" v-validate="'required'" class="form-control" id="name"
                                        name="name" value="{{ old('name') ?: $company->name }}"
                                        data-vv-as="&quot;{{ __('admin::app.settings.companies.name') }}&quot;" />
                                    <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name')
                                        }}</span>
                                </div>

                                <div class="form-group" :class="[errors.has('domain') ? 'has-error' : '']">
                                    <label for="domain"
                                        class="required">{{ __('admin::app.settings.companies.domain') }}</label>
                                    <input type="text" v-validate="'required'" class="form-control" id="domain"
                                        name="domain" value="{{ old('domain') ?: $company->domain }}"
                                        data-vv-as="&quot;{{ __('admin::app.settings.companies.domain') }}&quot;" />
                                    <span class="control-error" v-if="errors.has('domain')">@{{ errors.first('domain')
                                        }}</span>
                                    <small class="form-text text-muted">Subdomain for the tenant.</small>
                                </div>

                                <div class="form-group">
                                    <label for="status">{{ __('admin::app.settings.companies.status') }}</label>
                                    <label class="switch">
                                        <input type="checkbox" id="status" name="status" value="1" {{ $company->status ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                {!! view_render_event('saas.companies.edit.form_fields.after', ['company' => $company]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</x-admin::layouts>