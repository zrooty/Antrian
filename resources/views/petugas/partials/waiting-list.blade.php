<!-- Daftar Antrian Menunggu -->
<div class="bg-gray-50 dark:bg-gray-900/50 rounded-3xl p-8 border border-gray-100 dark:border-gray-800">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Daftar Tunggu (Today)</h3>
        <span class="px-3 py-1 bg-gray-200 dark:bg-gray-800 rounded-full text-xs font-bold text-gray-600 dark:text-gray-400">
            {{ $waitingQueues->count() }} Orang
        </span>
    </div>

    @if($waitingQueues->isEmpty())
        <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            <p class="text-gray-400 italic">Semua antrian sudah dipanggil :)</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($waitingQueues as $queue)
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 flex justify-between items-center shadow-sm">
                    <div>
                        <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $queue->nomor_antrian }}</div>
                        <div class="text-xs text-gray-400 font-medium">{{ $queue->user->name }}</div>
                    </div>
                    <div class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] uppercase font-bold rounded">
                        {{ $queue->service->kode_prefix ?? '-' }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div id="waiting-pagination-container" class="mt-6">
            {{ $waitingQueues->links() }}
        </div>
    @endif
</div>
