<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth;

use App\Actions\FilamentJet\CreateNewUser;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Redirector;
use Phpsa\FilamentPasswordReveal\Password;

class Register extends CardPage
{
    use WithRateLimiting;
    use RedirectsActions;

    protected static string $view = 'filament-jet::filament.pages.auth.register';

    public null|string $email = '';

    public null|string $name = '';

    public null|string $password = '';

    public null|string $passwordConfirmation = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    protected function getCardWidth(): string
    {
        return Features::getOption(Features::registration(), 'card_width');
    }

    protected function hasBrand(): bool
    {
        return Features::optionEnabled(Features::registration(), 'has_brand');
    }

    public function register(CreateNewUser $creator): ?Redirector
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::registration(), 'rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::registration(), 'rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/register.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        $user = $creator->create($data);

        Filament::auth()->login(
            user: $user,
            remember: true
        );

        session()->regenerate();

        return $this->redirectPath($creator);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('filament-jet::auth/register.fields.name.label'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('filament-jet::auth/register.fields.email.label'))
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(FilamentJet::userModel()),
            Password::make('password')
                ->label(__('filament-jet::auth/register.fields.password.label'))
                ->required()
                ->same('passwordConfirmation')
                ->revealable()
                ->copyable()
                ->generatable()
                ->rules(FilamentJet::getPasswordRules()),
            Password::make('passwordConfirmation')
                ->label(__('filament-jet::auth/register.fields.passwordConfirmation.label'))
                ->required()
                ->revealable(),
            Checkbox::make('terms')
                ->label(
                    new HtmlString(
                        __('filament-jet::auth/register.fields.terms_and_policy.label', [
                            'terms_of_service' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'terms').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::auth/register.fields.terms_and_policy.terms_of_service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'policy').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::auth/register.fields.terms_and_policy.privacy_policy').'</a>',
                        ])
                    )
                )
                ->rules(
                    FilamentJet::hasTermsAndPrivacyPolicyFeature()
                        ? ['accepted', 'required']
                        : []
                )
                ->validationAttribute('terms')
                ->visible(FilamentJet::hasTermsAndPrivacyPolicyFeature()),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        return [
            'password.same' => __('validation.confirmed', ['attribute' => __('filament-jet::auth/register.fields.password.validation_attribute')]),
        ];
    }

    public function getTitle(): string
    {
        return __('filament-jet::auth/register.title');
    }

    public function getHeading(): string
    {
        return __('filament-jet::auth/register.heading');
    }
}
