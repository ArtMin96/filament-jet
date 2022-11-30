<?php

namespace ArtMin96\FilamentJet;

class RouteActions
{
    public function routePrefix(): string
    {
        return config('filament-jet.route_group_prefix');
    }

    public function loginRoute(): string
    {
        return route('filament.auth.login');
    }

    public function registrationRoute(): string
    {
        return route($this->routePrefix().'auth.register');
    }

    public function getRequestPasswordResetRoute(): string
    {
        return route($this->routePrefix().'auth.password-reset.request');
    }

    public function emailVerificationPromptRoute(): string
    {
        return $this->routePrefix().'auth.email-verification.prompt';
    }
}
