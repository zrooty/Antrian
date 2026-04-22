<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-8 text-center flex flex-col items-center justify-center min-h-[400px]">
    <div class="bg-indigo-100 dark:bg-indigo-900/30 p-6 rounded-full mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
    </div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Belum Ada Antrian</h3>
    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md">Anda belum mengambil nomor antrian hari ini. Silakan buat reservasi antrian secara online untuk mendapatkan layanan.</p>
    <a href="{{ route('reservasi.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg">
        AMBIL NOMOR ANTRIAN
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
    </a>
</div>
