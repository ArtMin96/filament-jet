@php
    $teams = \Filament\Facades\Filament::auth()->user()->allTeams();
@endphp

@if ($teams->isNotEmpty())
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger" class="ml-4">
            <button  @class([
                'flex flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 items-center justify-center',
                'dark:bg-gray-900' => config('filament.dark_mode'),
            ]) aria-label="{{ __('filament::layout.buttons.user_menu.label') }}">
                @svg('heroicon-o-users', 'w-4 h-4')
            </button>
        </x-slot>
        <x-filament::dropdown.list>
            @foreach ($teams as $team)
                <form method="POST" action="{{ route(config('filament-jet.route_group_prefix').'current-team.update') }}" x-data>
                    @method('PUT')
                    @csrf

                    <!-- Hidden Team ID -->
                    <input type="hidden" name="team_id" value="{{ $team->id }}">

                    <x-filament::dropdown.item
                        :color="'secondary'"
                        :href="'#'"
                        :icon="\Filament\Facades\Filament::auth()->user()->isCurrentTeam($team) ? 'heroicon-o-check-circle' : ''"
                        tag="a"
                        x-on:click.prevent="$root.submit();"
                    >
                        {{ $team->name }}
                    </x-filament::dropdown.item>
                </form>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif
