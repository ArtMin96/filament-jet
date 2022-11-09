<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\View\View;
use Livewire\Component;

class Verify extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $hasBeenSent = false;

    public function mount()
    {
        if (Filament::auth()->check() && Filament::auth()->user()?->hasVerifiedEmail()) {
            return redirect(config('filament.home_url'));
        } elseif (! Filament::auth()->check()) {
            // User is not logged in...
            return redirect(route('filament.auth.login'));
        }
    }

    public function logout()
    {
        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('filament.auth.login');
    }

    public function resend()
    {
        Filament::auth()
            ->user()
            ->sendEmailVerificationNotification();

        Notification::make()->title(__('filament-jet::email-verify.notification_resend'))->success()->send();

        $this->hasBeenSent = true;
    }

    public function render(): View
    {
        $view = view('filament-jet::livewire.auth.verify');

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-jet::email-verify.title'),
        ]);

        return $view;
    }
}
