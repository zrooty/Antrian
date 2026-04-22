<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Petugas') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-sm font-bold border border-blue-200 dark:border-blue-800">
                    {{ $counter->name ?? 'Belum Ada Loket' }} ({{ $counter->code ?? '-' }})
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Control Panel -->
                <div class="lg:col-span-2 space-y-8">
                    @include('petugas.partials.active-queue')
                    @include('petugas.partials.waiting-list')
                </div>

                <!-- Sidebar: Skipped/History -->
                <div class="space-y-8">
                    @include('petugas.partials.skipped-list')
                    @include('petugas.partials.handled-list')
                </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
