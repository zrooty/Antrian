<x-admin-layout>
    <x-slot name="header">
        {{ __('Live Monitoring') }}
    </x-slot>

    <div x-data="liveMonitoring()" x-init="startPolling()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Loket -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div x-show="loading" class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center backdrop-blur-sm z-10 transition-opacity">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-ping mr-2"></span>
                    Status Loket Aktif
                </h3>
                
                <!-- If no counters active -->
                <div x-show="!loading && counters.length === 0" style="display: none;" class="text-center py-8 text-gray-500 italic">
                    Belum ada loket yang aktif.
                </div>

                <div x-show="counters.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="loket in counters" :key="loket.id">
                        <div class="p-4 border rounded-xl flex justify-between items-center transition-all duration-300"
                             :class="loket.active_queue ? 'border-indigo-200 dark:border-indigo-900 bg-indigo-50 dark:bg-indigo-900/20 shadow-md ring-1 ring-indigo-500/20' : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50'">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider" x-text="loket.name"></p>
                                <h5 class="text-xl font-black text-gray-900 dark:text-white transition-all duration-300" 
                                    x-text="loket.active_queue ? loket.active_queue.nomor_antrian : 'Siap Melayani'"
                                    :class="loket.active_queue ? 'text-indigo-600 dark:text-indigo-400 text-2xl scale-105 transform origin-left' : ''"></h5>
                                <p x-show="loket.active_queue" class="text-xs font-semibold text-gray-600 dark:text-gray-300 mt-1" x-text="loket.active_queue?.patient_name"></p>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-2">
                                <span x-show="!loket.active_queue" class="px-2 py-1 text-[10px] font-bold bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg uppercase transition">Kosong</span>
                                <span x-show="loket.active_queue && loket.active_queue.status === 'called'" class="px-2 py-1 text-[10px] font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-lg uppercase animate-pulse transition">Memanggil</span>
                                <span x-show="loket.active_queue && loket.active_queue.status === 'processing'" class="px-2 py-1 text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-lg uppercase transition">Melayani</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Live Queue List -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm h-full flex flex-col relative overflow-hidden">
                <div x-show="loading" class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center backdrop-blur-sm z-10 transition-opacity">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center justify-between">
                    Antrian Menunggu
                    <span x-show="!loading && waiting_queues.length > 0" style="display: none;" class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-[10px] rounded-lg font-black uppercase tracking-wider" x-text="waiting_queues.length + ' Orang'"></span>
                </h3>
                
                <div x-show="!loading && waiting_queues.length === 0" style="display: none;" class="flex-1 flex flex-col items-center justify-center text-gray-500 italic py-12">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <span>Tidak ada antrian menunggu.</span>
                </div>

                <div x-show="waiting_queues.length > 0" class="space-y-3 flex-1 overflow-y-auto pr-2 max-h-[500px] pb-4" id="waiting-queue-list">
                    <template x-for="queue in waiting_queues" :key="queue.id">
                        <div class="flex items-center space-x-4 p-3 border border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition cursor-default group bg-white dark:bg-gray-800 shadow-sm">
                            <div class="flex-shrink-0 w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 flex items-center justify-center rounded-xl text-indigo-600 dark:text-indigo-400 font-black text-lg group-hover:scale-105 transition-transform">
                                <span x-text="queue.nomor_antrian"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="queue.patient_name"></p>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="queue.service_name"></p>
                            </div>
                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700/50 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600/50" x-text="queue.time"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Inject Alpine.js data -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveMonitoring', () => ({
                counters: [],
                waiting_queues: [],
                loading: true,
                intervalId: null,

                async fetchData() {
                    try {
                        const response = await fetch('{{ route('api.admin.monitoring') }}');
                        if (!response.ok) throw new Error('Network response was not ok');
                        const json = await response.json();
                        this.counters = json.data.counters;
                        this.waiting_queues = json.data.waiting_queues;
                        this.loading = false;
                    } catch (error) {
                        console.error("Failed to fetch monitoring data:", error);
                    }
                },

                startPolling() {
                    this.fetchData();
                    this.intervalId = setInterval(() => {
                        this.fetchData();
                    }, 3000);
                },

                destroy() {
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                    }
                }
            }));
        });
    </script>
</x-admin-layout>
