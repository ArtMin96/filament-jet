@props([
    'percentage' => 0,
    'failed' => false
])

@php
    $done = $failed || $percentage == 100;
@endphp

<div {{ $attributes->merge(['class' => ' space-y-1'])->whereDoesntStartWith('wire:poll') }}

    {{-- Removes wire:poll directive when done --}}
    {{ !$done ? $attributes->whereStartsWith('wire:poll') : null }}
>

    <div class="flex justify-end space-y-1 text-xs font-semibold {{ $done ? 'text-green-500' : 'text-orange-600' }}">

        {{-- checkmark --}}
        @if($done) &#10003; @endif

        {{ $percentage }}%

    </div>

    <div class="flex h-2 overflow-hidden rounded bg-brand-100">
        <div style="transform: scale({{ $percentage / 100 }}, 1)" class="{{ $done ? 'bg-green-500' : 'bg-orange-500' }} transition-transform origin-left duration-200 ease-in-out w-full shadow-none flex flex-col"></div>
    </div>
</div>
