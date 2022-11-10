<div class="col-span-2 sm:col-span-1 flex justify-between">
    <div>
        <h3 @class([
            'text-xl font-bold text-gray-900 filament-jet-section-title',
            'dark:text-white' => config('filament.dark_mode')
        ])>
            {{ $title }}
        </h3>

        <p @class([
            'mt-1 text-base text-gray-600 filament-jet-grid-description',
            'dark:text-gray-100' => config('filament.dark_mode')
        ])>
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>
