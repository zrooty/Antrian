<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel Admin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar Toggle -->
        <button data-drawer-target="separator-sidebar" data-drawer-toggle="separator-sidebar" aria-controls="separator-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
        </button>

        <aside id="separator-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
            <div class="h-full px-3 py-4 overflow-y-auto bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="flex items-center ps-2.5 mb-5">
                    <x-application-logo class="h-8 me-3 fill-current text-indigo-600 dark:text-indigo-400" />
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Admin Panel</span>
                </a>
                <ul class="space-y-2 font-medium">
                    <li>
                        <x-sidebar-link :href="route('admin.rekap')" :active="request()->routeIs('admin.rekap')">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                                <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                                <path d="M12.5 0c-.157 0-.311.01-.462.03a1 1 0 0 0-.815.815c-.019.151-.029.305-.029.462v6a1 1 0 0 0 1 1h6c.157 0 .311-.01.462-.03a1 1 0 0 0 .815-.815c.019-.151.029-.305.029-.462V1a1 1 0 0 0-1-1h-6Z"/>
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link :href="route('admin.monitoring')" :active="request()->routeIs('admin.monitoring')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H7V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2ZM2 18V7h16v11H2Z"/>
                            </svg>
                            <span class="ms-3">Monitoring</span>
                        </x-sidebar-link>
                    </li>
                    <li x-data="{ open: {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.counters.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                                <path d="M15.977.783A1 1 0 0 0 15 0H3a1 1 0 0 0-.977.783L.2 9h4.285a3.99 3.99 0 0 1 3.515 2.114L9.414 13.9a1 1 0 0 0 1.172 0l1.414-2.786A3.99 3.99 0 0 1 15.515 9h4.285L15.977.783Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Manajemen Data</span>
                            <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul x-show="open" x-transition.opacity.duration.300ms class="py-2 space-y-2">
                            <li>
                                <a href="{{ route('admin.services.index') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.services.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-bold' : 'text-gray-900 dark:text-white' }}">Data Layanan</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.counters.index') }}" class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.counters.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-bold' : 'text-gray-900 dark:text-white' }}">Data Loket</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <x-sidebar-link :href="route('admin.operational')" :active="request()->routeIs('admin.operational')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"/>
                            </svg>
                            <span class="ms-3">Operasional</span>
                        </x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z"/>
                            </svg>
                            <span class="ms-3">Laporan</span>
                        </x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                            </svg>
                            <span class="ms-3">Akses & User</span>
                        </x-sidebar-link>
                    </li>
                </ul>
                <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                                <path d="M6.737 11.052a4.135 4.135 0 0 1-1.052-1.052 3.1 3.1 0 0 0-4.633.003l.003.003a3.1 3.1 0 0 0-.003 4.633l.003.003a3.1 3.1 0 0 0 4.633-.003l.003-.003a3.1 3.1 0 0 0-.003-4.633l-.003-.003Z"/>
                                <path d="M14.052 6.737a4.135 4.135 0 0 1 1.052 1.052 3.1 3.1 0 0 0 4.633-.003l-.003-.003a3.1 3.1 0 0 0 .003-4.633l-.003-.003a3.1 3.1 0 0 0-4.633.003l-.003.003a3.1 3.1 0 0 0 .003 4.633l.003.003Z"/>
                                <path d="M11.052 14.052a4.135 4.135 0 0 1 1.052 1.052 3.1 3.1 0 0 0 4.633-.003l-.003-.003a3.1 3.1 0 0 0 .003-4.633l-.003-.003a3.1 3.1 0 0 0-4.633.003l-.003.003a3.1 3.1 0 0 0 .003 4.633l.003.003Z"/>
                            </svg>
                            <span class="ms-3">Pengaturan</span>
                        </x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-1-8h2v3a1 1 0 0 1-2 0v-3Zm0-3h2v1a1 1 0 0 1-2 0V7Z"/>
                            </svg>
                            <span class="ms-3">Log Aktivitas</span>
                        </x-sidebar-link>
                    </li>
                </ul>

                <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <button x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                            @click="isDark = !isDark; document.documentElement.classList.toggle('dark'); localStorage.theme = isDark ? 'dark' : 'light'"
                            type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        <!-- Moon icon (shows in light mode) -->
                        <svg x-show="!isDark" class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <!-- Sun icon (shows in dark mode) -->
                        <svg x-show="isDark" style="display: none;" class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.364a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.415l-.707-.707a1 1 0 010-1.415zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-2.565 4.93a1 1 0 01-.707-.708l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 01-.707 1.414zM10 18a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zm-4.22-2.364a1 1 0 01-1.415 0l-.707-.707a1 1 0 111.414-1.415l.707.707a1 1 0 010 1.415zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.364-4.22a1 1 0 01.708-.707l.707-.707a1 1 0 111.414 1.414l-.707.707a1 1 0 01-1.414-1.414zM10 5a5 5 0 100 10 5 5 0 000-10z"></path>
                        </svg>
                        <span class="ms-3 font-medium" x-text="isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-red-50 dark:text-white dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 font-semibold">
                            <svg class="flex-shrink-0 w-5 h-5 text-red-600 dark:text-red-400 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                            </svg>
                            <span class="ms-3">Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="p-4 sm:ml-64">
            <div class="p-4 rounded-lg mt-1">
                <!-- Page Heading -->
                @isset($header)
                    <header class="mb-6">
                        <div class="max-w-7xl mx-auto">
                            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                                {{ $header }}
                            </h2>
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
