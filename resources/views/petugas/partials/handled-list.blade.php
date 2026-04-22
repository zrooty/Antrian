<!-- Riwayat Pasien Ditangani -->
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700">
    <h3 class="font-bold text-gray-800 dark:text-white mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Riwayat Hari Ini
    </h3>
    
    <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
        @forelse($handledQueues as $queue)
            <div class="bg-green-50 dark:bg-green-900/10 p-4 rounded-2xl border border-green-100 dark:border-green-900/30 flex items-center justify-between">
                <div>
                    <div class="font-black text-lg text-green-700 dark:text-green-400">{{ $queue->nomor_antrian }}</div>
                    <div class="text-xs text-green-600/70 dark:text-green-400/50">{{ $queue->user->name }}</div>
                </div>
                <div class="text-xs font-bold text-green-500 bg-white dark:bg-gray-800 px-2 py-1 rounded-md shadow-sm">
                    {{ $queue->updated_at->format('H:i') }}
                </div>
            </div>
        @empty
            <div class="text-center py-6">
                <p class="text-sm text-gray-400 italic">Belum ada pasien ditangani.</p>
            </div>
        @endforelse
    </div>
</div>
