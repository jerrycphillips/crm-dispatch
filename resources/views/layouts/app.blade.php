<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'TailAdmin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body
    x-data="{ page: 'ecommerce', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false, scrollTop: false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
            $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
>
    {{-- Preloader --}}
    @include('partials.preloader')

    {{-- Page Wrapper --}}
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Content Area --}}
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">

            {{-- Overlay (for mobile) --}}
            @include('partials.overlay')

            {{-- Header --}}
            @include('partials.header')

            {{-- Main Content --}}
            <main>
                <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>