<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait CanLogoutOtherBrowserSessions
{
    /**
     * Log out from other browser sessions.
     *
     * @return void
     */
    public function logoutOtherBrowserSessions()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        Auth::logoutOtherDevices('password');

        $this->deleteOtherSessionRecords();

        request()->session()->put([
            'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
        ]);

        $this->emit('loggedOut');

        Notification::make()
            ->title(__('filament-jet::account/browser-sessions.messages.cleared'))
            ->success()
            ->send();
    }

    /**
     * Delete the other browser session records from storage.
     *
     * @return void
     */
    protected function deleteOtherSessionRecords()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }
}
