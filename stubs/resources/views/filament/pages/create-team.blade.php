<x-filament::page>
    <x-filament-jet-form-section submit="createTeam">
        <x-slot name="title">
            {{ __('filament-jet::teams/create.title') }}
        </x-slot>

        <x-slot name="description">
            {{ __('filament-jet::teams/create.description') }}
        </x-slot>

        <x-slot name="form">
            <!-- Team Owner Information -->
            <div class="col-span-6">
                <div class="block font-medium text-sm text-gray-700">
                    {{ __('filament-jet::teams/create.team_owner_label') }}
                </div>

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}">

                    <div class="ml-4 leading-tight">
                        <div>{{ $this->user->name }}</div>
                        <div class="text-gray-700 text-sm">{{ $this->user->email }}</div>
                    </div>
                </div>
            </div>

            {{ $this->createTeamForm }}
        </x-slot>

        <x-slot name="actions">
            <x-filament::button type="submit">
                {{ __('filament-jet::teams/create.buttons.save') }}
            </x-filament::button>
        </x-slot>
    </x-filament-jet-form-section>
</x-filament::page>
