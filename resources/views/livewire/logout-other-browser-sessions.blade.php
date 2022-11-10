@if (count($this->sessions) > 0)
    <div class="mt-5 space-y-6">
        <!-- Other Browser Sessions -->
        @foreach ($this->sessions as $session)
            <div class="flex items-center">
                <div>
                    @if ($session->agent->isDesktop())
                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-500">
                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500">
                            <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
                        </svg>
                    @endif
                </div>

                <div class="ml-4">
                    <div class="text-sm text-gray-600 dark:text-white">
                        {{ $session->agent->platform() ? $session->agent->platform() : __('filament-jet::account.other_browser_sessions.unknown_device') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('filament-jet::account.other_browser_sessions.unknown_device') }}
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-300">
                            {{ $session->ip_address }},

                            @if ($session->is_current_device)
                                <span class="text-success-500 font-semibold">{{ __('filament-jet::account.other_browser_sessions.this_device') }}</span>
                            @else
                                {{ __('filament-jet::account.other_browser_sessions.last_active') }} {{ $session->last_active }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div></div>
@endif
