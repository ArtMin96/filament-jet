@props(['code', 'hiddenCodeSymbol' => '&lowast;', 'hiddenCodeSymbolTimes' => 12])

<div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-300">
    <div class="flex items-center">
        <p class="font-semibold mr-2">
            {{ __('filament-jet::account.2fa.setup_key') }}:
        </p>

        <div x-data="{ displayTwoFactorSecret: false }" class="flex items-center">
            <span class="mr-2"
                  x-html="displayTwoFactorSecret ? '{{ $code }}' : '{{ str_repeat($hiddenCodeSymbol, $hiddenCodeSymbolTimes) }}'"></span>

            <x-heroicon-s-eye class="w-6 h-6 cursor-pointer"
                              x-show="! displayTwoFactorSecret"
                              x-on:click="displayTwoFactorSecret = ! displayTwoFactorSecret" />
            <x-heroicon-s-eye-off class="w-6 h-6 cursor-pointer"
                                  x-show="displayTwoFactorSecret"
                                  x-on:click="displayTwoFactorSecret = ! displayTwoFactorSecret" />
        </div>
    </div>
</div>
