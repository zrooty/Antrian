<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Pasien') }}
        </h2>
        @if($activeQueue && in_array($activeQueue->status, ['waiting', 'called', 'processing', 'skipped']))
            <!-- Auto refresh handled by JS countdown -->
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            @error('date')
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $message }}</span>
                </div>
            @enderror

            {{-- KONDISI A: TIDAK ADA ANTRIAN AKTIF & TIDAK ADA RIWAYAT --}}
            @if(!$activeQueue && !$doneQueue)
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
            @endif

            {{-- KONDISI B: ADA ANTRIAN AKTIF --}}
            @if($activeQueue)
                @php
                    $badgeColors = [
                        'waiting' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                        'called' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-blue-200 dark:border-blue-800 animate-pulse',
                        'processing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                        'skipped' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 border-red-200 dark:border-red-800',
                    ];
                    $badgeColor = $badgeColors[$activeQueue->status] ?? 'bg-gray-100 text-gray-800';
                    
                    $labels = [
                        'waiting' => 'MENUNGGU PANGGILAN',
                        'called' => 'NOMOR DIPANGGIL',
                        'processing' => 'SEDANG DILAYANI',
                        'skipped' => 'TERLEWAT',
                    ];
                    $badgeLabel = $labels[$activeQueue->status] ?? strtoupper($activeQueue->status);
                @endphp

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-100 dark:border-gray-700 relative">
                    <!-- Top accent line -->
                    <div id="top-accent-line" class="h-2 w-full {{ $activeQueue->status === 'called' ? 'bg-blue-500' : ($activeQueue->status === 'skipped' ? 'bg-red-500' : 'bg-indigo-500') }}"></div>
                    
                    <div class="p-8">
                        <!-- Header & Badge -->
                        <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="text-center md:text-left mb-4 md:mb-0">
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($activeQueue->schedule->tanggal)->translatedFormat('l, d F Y') }}</p>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeQueue->service->nama_layanan }}</h1>
                            </div>
                            <div id="status-badge" class="px-4 py-2 rounded-full border font-bold text-sm tracking-widest {{ $badgeColor }}">
                                {{ $badgeLabel }}
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
            @endif

            {{-- KONDISI C: ANTRIAN SUDAH SELESAI HARI INI (Tapi tidak ada antrian aktif) --}}
            @if($doneQueue && !$activeQueue)
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
            @endif

            <!-- Riwayat Kunjungan -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Riwayat Kunjungan</h3>
                </div>
                
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Layanan</th>
                                <th class="px-6 py-4 font-semibold">No. Antrian</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($historyQueues as $history)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $history->schedule ? \Carbon\Carbon::parse($history->schedule->tanggal)->translatedFormat('d M Y') : $history->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $history->service->nama_layanan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $history->nomor_antrian }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($history->status === 'done')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded text-xs font-bold">SELESAI</span>
                                        @elseif($history->status === 'skipped')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded text-xs font-bold">DILEWATI</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded text-xs font-bold uppercase">{{ $history->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm italic">
                                        Belum ada riwayat kunjungan sebelumnya.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @if($activeQueue && in_array($activeQueue->status, ['waiting', 'called', 'processing', 'skipped']))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownElement = document.getElementById('refresh-countdown');
            const statusBadge = document.getElementById('status-badge');
            const topAccent = document.getElementById('top-accent-line');
            const queueNumberText = document.getElementById('queue-number-text');
            const dynamicContent = document.getElementById('dynamic-status-content');
            
            let timeLeft = 10;

            function fetchQueueStatus() {
                fetch('{{ route('dashboard.api.status') }}')
                    .then(response => response.json())
                    .then(data => {
                        // Reset countdown visually
                        timeLeft = 10;
                        if (countdownElement) countdownElement.textContent = timeLeft;

                        if (!data.activeQueue) {
                            // If queue is done or deleted, reload to show Condition C or A
                            window.location.reload();
                            return;
                        }

                        const q = data.activeQueue;
                        
                        // 1. Update Number
                        if (queueNumberText) queueNumberText.innerText = q.nomor_antrian;

                        // 2. Update Badge & Accent
                        if (statusBadge) {
                            updateBadge(statusBadge, q.status);
                        }
                        if (topAccent) {
                            updateAccent(topAccent, q.status);
                        }

                        // 3. Update Dynamic Content
                        updateDynamicContent(dynamicContent, q, data.position, data.estimasi);

                        // 4. Update History Table
                        if (data.historyQueues) {
                            updateHistoryTable(data.historyQueues);
                        }
                    })
                    .catch(error => console.error("Update failed:", error));
            }

            function updateHistoryTable(history) {
                const tbody = document.getElementById('history-table-body');
                if (!tbody) return;

                if (history.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm italic">
                                Belum ada riwayat kunjungan sebelumnya.
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                history.forEach(item => {
                    const date = item.schedule ? formatDate(item.schedule.tanggal) : formatDate(item.created_at);
                    const statusHtml = getStatusBadge(item.status);
                    
                    html += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${date}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${item.service ? item.service.nama_layanan : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                ${item.nomor_antrian}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                ${statusHtml}
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                const options = { day: '2-digit', month: 'short', year: 'numeric' };
                // Simple formatting for now
                return d.toLocaleDateString('id-ID', options);
            }

            function getStatusBadge(status) {
                if (status === 'done') {
                    return '<span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded text-xs font-bold">SELESAI</span>';
                } else if (status === 'skipped') {
                    return '<span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded text-xs font-bold">DILEWATI</span>';
                } else {
                    return `<span class="px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded text-xs font-bold uppercase">${status}</span>`;
                }
            }

            function updateBadge(el, status) {
                const colors = {
                    waiting: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                    called: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-blue-200 dark:border-blue-800 animate-pulse',
                    processing: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                    skipped: 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 border-red-200 dark:border-red-800'
                };
                const labels = {
                    waiting: 'MENUNGGU PANGGILAN',
                    called: 'NOMOR DIPANGGIL',
                    processing: 'SEDANG DILAYANI',
                    skipped: 'TERLEWAT'
                };
                el.className = `px-4 py-2 rounded-full border font-bold text-sm tracking-widest ${colors[status] || ''}`;
                el.innerText = labels[status] || status.toUpperCase();
            }

            function updateAccent(el, status) {
                const colors = {
                    called: 'bg-blue-500',
                    skipped: 'bg-red-500',
                    waiting: 'bg-indigo-500',
                    processing: 'bg-indigo-500'
                };
                el.className = `h-2 w-full ${colors[status] || 'bg-indigo-500'}`;
            }

            function updateDynamicContent(el, q, position, estimasi) {
                if (q.status === 'waiting') {
                    el.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 text-center">
                            <div>
                                <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Prakiraan Posisi</p>
                                <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">Ke-${position}</p>
                            </div>
                            <div class="hidden md:block w-px bg-yellow-200 dark:bg-yellow-800/50 mx-auto"></div>
                            <div class="block md:hidden h-px bg-yellow-200 dark:bg-yellow-800/50 w-full my-2"></div>
                            <div>
                                <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Estimasi Tunggu</p>
                                <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">± ${estimasi} <span class="text-lg font-bold">menit</span></p>
                            </div>
                        </div>
                    `;
                } else if (q.status === 'called') {
                    el.innerHTML = `
                        <div class="p-8 rounded-2xl bg-blue-600 text-white text-center shadow-xl animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <h3 class="text-3xl font-black uppercase tracking-wider mb-2">GILIRAN ANDA!</h3>
                            <p class="text-blue-100 text-lg mb-6">Silakan segera menuju ke:</p>
                            <div class="inline-block px-8 py-3 bg-white text-blue-800 rounded-full text-2xl font-black tracking-widest shadow-inner">
                                ${q.counter ? q.counter.name : 'LOKET'}
                            </div>
                        </div>
                    `;
                } else if (q.status === 'processing') {
                    el.innerHTML = `
                        <div class="p-8 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 text-center">
                            <h3 class="text-2xl font-bold text-indigo-800 dark:text-indigo-300 mb-2">Anda Sedang Dilayani</h3>
                            <p class="text-indigo-600 dark:text-indigo-400">Petugas di ${q.counter ? q.counter.name : 'Loket'} sedang memproses keperluan Anda.</p>
                        </div>
                    `;
                } else if (q.status === 'skipped') {
                    el.innerHTML = `
                        <div class="p-8 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-red-800 dark:text-red-400 mb-2">Nomor Anda Dilewati</h3>
                            <p class="text-red-600 dark:text-red-300">Panggilan untuk nomor Anda tidak direspons. Silakan melapor kepada petugas untuk melakukan pemanggilan ulang.</p>
                        </div>
                    `;
                }
            }

            // Polling interval
            setInterval(function() {
                timeLeft--;
                if (countdownElement) countdownElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    fetchQueueStatus();
                }
            }, 1000);
        });
    </script>
    @endif
</x-app-layout>
