<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use Closure;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

class AttemptToAuthenticate
{
    /**
     * The guard implementation.
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * @param array<string, string> $data
     * @param Closure               $next
     */
    public function handle(array $data, Closure $next)
    {
        if ($this->guard->attempt([
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ], $data['remember'])) {
            return $next($data);
        }

        $this->throwFailedAuthenticationException();
    }

    /**
     * Throw a failed authentication validation exception.
     */
    protected function throwFailedAuthenticationException(): void
    {
        throw ValidationException::withMessages([
            FilamentJet::username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param array<string, string> $data
     */
    protected function fireFailedEvent(array $data): void
    {
        event(new Failed(config('filament.auth.guard'), null, [
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ]));
    }
}
