<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Auth;

use App\Actions\FilamentJet\CreateNewUser;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Phpsa\FilamentPasswordReveal\Password;

class Register extends Component implements HasForms
{
    use InteractsWithForms;
    use PasswordValidationRules;

    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $terms;

    public function mount()
    {
        if (Filament::auth()->check()) {
            return redirect(config('filament.home_url'));
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('filament-jet::registration.fields.name'))
                ->maxLength(255)
                ->required(),
            TextInput::make('email')
                ->label(__('filament-jet::registration.fields.email'))
                ->required()
                ->rules(['email'])
                ->maxLength(255)
                ->unique(FilamentJet::userModel(), FilamentJet::email()),
            Password::make('password')
                ->label(__('filament-jet::registration.fields.password'))
                ->required()
                ->revealable()
                ->generatable()
                ->copyable()
                ->rules($this->passwordRules()),
            Password::make('password_confirmation')
                ->label(__('filament-jet::registration.fields.confirm_password'))
                ->required()
                ->revealable(),
            Checkbox::make('terms')
                ->label(
                    new HtmlString(
                        __('filament-jet::registration.fields.terms_and_policy.label', [
                            'terms_of_service' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'terms').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::registration.fields.terms_and_policy.terms_of_service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route(config('filament-jet.route_group_prefix').'policy').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('filament-jet::registration.fields.terms_and_policy.privacy_policy').'</a>',
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

    public function register(CreateNewUser $creator)
    {
        $formState = $this->form->getState();

        $user = $creator->create($formState);

        Filament::auth()->login($user, true);

        return redirect()->to(config('filament-jet.redirects.register'));
    }

    public function render()
    {
        $view = view('filament-jet::livewire.auth.register');

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-jet::registration.title'),
        ]);

        return $view;
    }
}
