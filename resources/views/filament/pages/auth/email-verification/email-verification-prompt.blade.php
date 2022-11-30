<div class="space-y-6">
    <p class="text-center text-sm text-gray-600 dark:text-gray-300">
        {{ __('filament-jet::auth/email-verification/email-verification-prompt.messages.notification_sent', [
            'email' => \Filament\Facades\Filament::auth()->user()->getEmailForVerification(),
        ]) }}
    </p>

    <p class="text-center text-sm text-gray-600 dark:text-gray-300">
        {{ __('filament-jet::auth/email-verification/email-verification-prompt.messages.notification_not_received') }} <x-filament::link wire:click="resendNotification" tag="button">{{ __('filament-jet::auth/email-verification/email-verification-prompt.buttons.resend_notification.label') }}.</x-filament::link>
    </p>
</div>
