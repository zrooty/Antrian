<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-100 dark:border-gray-700 relative">
    <!-- Top accent line -->
    <div id="top-accent-line" class="h-2 w-full {{ $activeQueue->accent_color }}"></div>
    
    <div class="p-8">
        <!-- Header & Badge -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-6 border-b border-gray-100 dark:border-gray-700">
            <div class="text-center md:text-left mb-4 md:mb-0">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($activeQueue->schedule->tanggal)->translatedFormat('l, d F Y') }}</p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeQueue->service->nama_layanan }}</h1>
            </div>
            <div id="status-badge" class="px-4 py-2 rounded-full border font-bold text-sm tracking-widest {{ $activeQueue->status_color }}">
                {{ $activeQueue->status_label }}
            </div>
        </div>

        <!-- Main Big Number -->
        <div class="text-center mb-12">
            <p class="text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-widest mb-4">Nomor Antrian Anda</p>
            <div class="inline-block py-6 px-16 rounded-3xl bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 shadow-inner">
                <span id="queue-number-text" class="text-7xl md:text-8xl font-black tracking-tight text-gray-900 dark:text-white">
                    {{ $activeQueue->nomor_antrian }}
                </span>
            </div>
        </div>

        <!-- Dynamic Content Based on Status -->
        <div id="dynamic-status-content">
            @if($activeQueue->status === 'waiting')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 text-center">
                    <div>
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Prakiraan Posisi</p>
                        <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">Ke-{{ $position }}</p>
                    </div>
                    <div class="hidden md:block w-px bg-yellow-200 dark:bg-yellow-800/50 mx-auto"></div>
                    <div class="block md:hidden h-px bg-yellow-200 dark:bg-yellow-800/50 w-full my-2"></div>
                    <div>
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Estimasi Tunggu</p>
                        <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">± {{ $estimasi }} <span class="text-lg font-bold">menit</span></p>
                    </div>
                </div>
            @elseif($activeQueue->status === 'called')
                <div class="p-8 rounded-2xl bg-blue-600 text-white text-center shadow-xl animate-pulse">
                    <!-- Icon Sound -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <h3 class="text-3xl font-black uppercase tracking-wider mb-2">GILIRAN ANDA!</h3>
                    <p class="text-blue-100 text-lg mb-6">Silakan segera menuju ke:</p>
                    <div class="inline-block px-8 py-3 bg-white text-blue-800 rounded-full text-2xl font-black tracking-widest shadow-inner">
                        {{ $activeQueue->counter->name ?? 'LOKET' }}
                    </div>
                </div>
            @elseif($activeQueue->status === 'processing')
                <div class="p-8 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 text-center">
                    <h3 class="text-2xl font-bold text-indigo-800 dark:text-indigo-300 mb-2">Anda Sedang Dilayani</h3>
                    <p class="text-indigo-600 dark:text-indigo-400">Petugas di {{ $activeQueue->counter->name ?? 'Loket' }} sedang memproses keperluan Anda.</p>
                </div>
            @elseif($activeQueue->status === 'skipped')
                <div class="p-8 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-xl font-bold text-red-800 dark:text-red-400 mb-2">Nomor Anda Dilewati</h3>
                    <p class="text-red-600 dark:text-red-300">Panggilan untuk nomor Anda tidak direspons. Silakan melapor kepada petugas untuk melakukan pemanggilan ulang.</p>
                </div>
            @endif
        </div>

        <p class="text-center mt-8 text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            Pembaruan otomatis dalam <span id="refresh-countdown" class="font-bold mx-1 text-gray-600 dark:text-gray-300">10</span> detik
        </p>

    </div>
</div>
