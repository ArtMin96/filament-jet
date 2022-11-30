<?php

namespace ArtMin96\FilamentJet\Actions\Auth;

use ArtMin96\FilamentJet\Events\TwoFactorAuthenticationChallenged;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\TwoFactorAuthenticatable;
use Closure;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Redirector;

class RedirectIfTwoFactorAuthenticatable
{
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
     * @param array   $data
     * @param Closure $next
     */
    public function handle(array $data, Closure $next)
    {
        $user = $this->validateCredentials($data);

        if (FilamentJet::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_secret &&
                ! is_null(optional($user)->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                return $this->twoFactorChallengeResponse($data, $user);
            } else {
                return $next($data);
            }
        }

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return $this->twoFactorChallengeResponse($data, $user);
        }

        return $next($data);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param  array<string, string>  $data
     */
    protected function validateCredentials(array $data)
    {
        $model = $this->guard->getProvider()->getModel();

        return tap($model::where(FilamentJet::username(), $data[FilamentJet::username()])->first(), function ($user) use ($data) {
            if (! $user || ! $this->guard->getProvider()->validateCredentials($user, ['password' => $data['password']])) {
                $this->fireFailedEvent($data, $user);

                $this->throwFailedAuthenticationException();
            }
        });
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
     * @param Authenticatable|null  $user
     */
    protected function fireFailedEvent(array $data, Authenticatable | Model | null $user = null): void
    {
        event(new Failed(config('filament.auth.guard'), $user, [
            FilamentJet::username() => $data[FilamentJet::username()],
            'password' => $data['password'],
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     *
     * @param array                 $data
     * @param Authenticatable|Model $user
     */
    protected function twoFactorChallengeResponse(array $data, Authenticatable | Model $user): Redirector | RedirectResponse
    {
        session()->put([
            jet()->getTwoFactorLoginSessionPrefix().'login.id' => $user->getKey(),
            jet()->getTwoFactorLoginSessionPrefix().'login.remember' => $data['remember'],
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return redirect()->route('auth.two-factor.login');
    }
}
