<div class="col-span-2 sm:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 @class([
            'text-xl font-bold text-gray-900 filament-account-section-title',
            'dark:text-white' => config('filament.dark_mode')
        ])>
            {{ $title }}
        </h3>

        <p @class([
            'mt-1 text-base text-gray-600 filament-breezy-grid-description',
            'dark:text-gray-100' => config('filament.dark_mode')
        ])>
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>
