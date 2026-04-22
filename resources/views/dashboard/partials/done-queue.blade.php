<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-8 text-center flex flex-col items-center justify-center">
    <div class="bg-green-100 dark:bg-green-900/30 p-6 rounded-full mb-6 text-green-600 dark:text-green-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Pelayanan Selesai</h3>
    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md">Terima kasih atas kunjungan Anda hari ini. Antrian layanan <span class="font-semibold">{{ $doneQueue->service->nama_layanan }} ({{ $doneQueue->nomor_antrian }})</span> telah diselesaikan.</p>
    
    <a href="{{ route('reservasi.create') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-bold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        Ambil Antrian Baru
    </a>
</div>
