<x-admin-layout>
    <x-slot name="header">
        {{ __('Live Monitoring') }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Loket -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-ping mr-2"></span>
                    Status Loket Aktif
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="counter-status-list">
                    <!-- Dynamic counters will appear here -->
                    <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Loket 1</p>
                            <h5 class="text-xl font-black text-gray-900 dark:text-white">A-001</h5>
                        </div>
                        <span class="px-2 py-1 text-[10px] font-bold bg-green-100 text-green-700 rounded-lg">Melayani</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Queue List -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm h-full">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Antrian Menunggu</h3>
                <div class="space-y-4" id="waiting-queue-list">
                    <!-- Dynamic waiting list -->
                    <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition cursor-default">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center rounded-lg text-indigo-600 dark:text-indigo-400 font-bold">
                            A2
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">Pasien John Doe</p>
                            <p class="text-xs text-gray-500 truncate">Layanan Umum</p>
                        </div>
                        <div class="text-xs text-gray-400">10:45</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
