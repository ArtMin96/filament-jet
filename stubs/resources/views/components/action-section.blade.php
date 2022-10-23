<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-6 filament-account-action-section']) }}>
    <x-filament-account-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-filament-account-section-title>

    <div class="col-span-2 sm:col-span-1 mt-5 md:mt-0">
        <x-filament::card>
            {{ $content }}

            @if (isset($actions))
                <x-slot name="footer">
                    <div class="text-right">
                        {{ $actions }}
                    </div>
                </x-slot>
            @endif
        </x-filament::card>
    </div>
</div>
