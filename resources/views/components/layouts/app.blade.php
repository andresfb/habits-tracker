<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="fantasy"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="/images/habits.png">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if($user = auth()->user())
                    <x-menu-separator />

                    <x-list-item :item="$user"
                                 value="name"
                                 sub-value="email"
                                 no-separator
                                 no-hover
                                 class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-button icon="o-power"
                                      class="btn-circle btn-ghost btn-xs"
                                      tooltip-left="logoff"
                                      no-wire-navigate
                                      link="/logout" />
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Trackers"
                             icon="o-bars-arrow-down"
                             link="{{ route('home', absolute: false) }}" />

                <x-menu-item title="Overview"
                             icon="o-sparkles"
                             link="{{ route('overview', absolute: false) }}" />

                <x-menu-item title="Habits"
                             icon="o-newspaper"
                             link="{{ route('habits', absolute: false) }}" />

                <x-menu-item title="Categories"
                             icon="o-tag"
                             link="{{ route('categories', absolute: false) }}" />

                <x-menu-item title="Units"
                             icon="o-adjustments-horizontal"
                             link="{{ route('units', absolute: false) }}" />

                <x-menu-item title="Periods"
                             icon="o-calendar-days"
                             link="{{ route('periods', absolute: false) }}" />

            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
