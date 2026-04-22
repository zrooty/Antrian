<!-- Sidebar: Skipped -->
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700">
    <h3 class="font-bold text-gray-800 dark:text-white mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Antrian Dilewati
    </h3>
    
    <div class="space-y-4">
        @forelse($skippedQueues as $queue)
            <div class="bg-red-50 dark:bg-red-900/10 p-4 rounded-2xl border border-red-100 dark:border-red-900/30 flex items-center justify-between group transition-all hover:bg-red-100 dark:hover:bg-red-900/20">
                <div>
                    <div class="font-black text-lg text-red-700 dark:text-red-400">{{ $queue->nomor_antrian }}</div>
                    <div class="text-xs text-red-600/70 dark:text-red-400/50">{{ $queue->user->name }}</div>
                </div>
                <form action="{{ route('petugas.recall', $queue->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="p-2 bg-white dark:bg-gray-800 text-red-600 rounded-lg shadow-sm border border-red-200 dark:border-red-800 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                    </button>
                </form>
            </div>
        @empty
            <div class="text-center py-6">
                <p class="text-sm text-gray-400 italic">Tidak ada data.</p>
            </div>
        @endforelse
    </div>
</div>
