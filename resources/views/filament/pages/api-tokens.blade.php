<x-filament::page>
    <x-filament-jet-form-section submit="createApiToken">
        <x-slot name="title">
            {{ __('filament-jet::api.create.title') }}
        </x-slot>

        <x-slot name="description">
            {{ __('filament-jet::api.create.description') }}
        </x-slot>

        <x-slot name="form">
            {{ $this->form }}
        </x-slot>

        <x-slot name="actions">
            <x-filament::button type="submit">
                {{ __('filament-jet::api.create.submit') }}
            </x-filament::button>
        </x-slot>
    </x-filament-jet-form-section>

    <x-tables::modal id="showing-token-modal" width="lg">
        <x-slot name="subheading">
            <h3 class="text-xl">{{ __('filament-jet::api.modal.title') }}</h3>
        </x-slot>

        <div>
            <p class="max-w-xl text-sm text-gray-600 dark:text-gray-300">{{ __('filament-jet::api.modal.description') }}</p>

            <x-filament-jet-token-field :value="$plainTextToken" autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
        </div>

        <x-slot name="actions">
            <x-filament::modal.actions alignment="right" fullWidth="false">
                <x-filament::button x-on:click.prevent="$dispatch('close-modal', {id: 'showing-token-modal'})" color="secondary">
                    {{ __('filament-jet::api.modal.buttons.close') }}
                </x-filament::button>
            </x-filament::modal.actions>
        </x-slot>
    </x-tables::modal>

    @livewire(\ArtMin96\FilamentJet\Http\Livewire\ApiTokensTable::class)
</x-filament::page>
