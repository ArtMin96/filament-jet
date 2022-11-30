<?php

namespace ArtMin96\FilamentJet\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;

class ResetPassword extends BaseNotification
{
    public string $url;

    protected function resetUrl($notifiable): string
    {
        return $this->url;
    }
}
