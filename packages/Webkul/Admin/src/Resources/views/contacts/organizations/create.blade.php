<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.organizations.create.title')
        </x-slot>

        {!! view_render_event('admin.organizations.create.form.before') !!}

        <x-admin::form :action="route('admin.contacts.organizations.store')" method="POST">

            <div class="flex flex-col gap-4">
                <div
                    class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-2">
                        {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                        <!-- Breadcrumbs -->
                        <x-admin::breadcrumbs name="contacts.organizations.create" />

                        {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                        <div class="text-xl font-bold dark:text-gray-300">
                            @lang('admin::app.contacts.organizations.create.title')
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2.5">
                        <div class="flex items-center gap-x-2.5">
                            {!! view_render_event('admin.organizations.create.save_buttons.before') !!}

                            <!-- Create button for person -->
                            <button type="submit" class="primary-button">
                                @lang('admin::app.contacts.organizations.create.save-btn')
                            </button>

                            {!! view_render_event('admin.organizations.create.save_buttons.before') !!}
                        </div>
                    </div>
                </div>

                <div
                    class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    {!! view_render_event('admin.contacts.organizations.create.form_controls.before') !!}

                    <x-admin::attributes :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'organizations',
                    ])" :custom-validations="[
                        'name' => [
                            'max:100',
                        ],
                        'slug' => [
                            'max:100',
                            'unique:organizations,slug',
                        ],
                        'address' => [
                            'max:100',
                        ],
                        'postcode' => [
                            'postcode',
                        ],
                    ]" />

                    <!-- Slug Manual Input -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Slug
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="slug"
                            :value="old('slug')"
                            label="Slug"
                            placeholder="Slug"
                            @input="isSlugManuallyChanged = true"
                        />

                        <x-admin::form.control-group.error control-name="slug" />
                    </x-admin::form.control-group>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            let isSlugManuallyChanged = false;
                            const nameInput = document.querySelector('[name="name"]');
                            const slugInput = document.querySelector('[name="slug"]');

                            if (nameInput && slugInput) {
                                nameInput.addEventListener('input', function() {
                                    if (!isSlugManuallyChanged) {
                                        let slug = nameInput.value
                                            .toLowerCase()
                                            .replace(/[^\w ]+/g, '')
                                            .replace(/ +/g, '-');
                                        
                                        slugInput.value = slug;
                                    }
                                });

                                slugInput.addEventListener('input', function() {
                                    isSlugManuallyChanged = true;
                                });
                            }
                        });
                    </script>

                    {!! view_render_event('admin.contacts.organizations.edit.form_controls.after') !!}
                </div>
            </div>
        </x-admin::form>

        {!! view_render_event('admin.organizations.create.form.after') !!}
</x-admin::layouts>