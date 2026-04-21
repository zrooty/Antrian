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
                    <!-- Unit Antrian Aktif -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700">
                        <div class="p-8">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6">Antrian Sedang Dilayani</h3>
                            
                            @if($activeQueue)
                                <div class="flex flex-col items-center py-10">
                                    <div class="text-xs font-bold text-gray-400 mb-2 uppercase">{{ $activeQueue->service->nama_layanan ?? 'Umum' }}</div>
                                    <div class="text-8xl font-black text-blue-600 dark:text-blue-400 mb-4">{{ $activeQueue->nomor_antrian }}</div>
                                    <div class="text-xl font-medium text-gray-600 dark:text-gray-300 mb-8">{{ $activeQueue->user->name }}</div>
                                    
                                    <div class="flex flex-wrap justify-center gap-4">
                                        @if($activeQueue->status === 'called')
                                            <!-- Tombol Panggil Ulang Suara -->
                                            <form action="{{ route('petugas.recall', $activeQueue->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                                    Panggil Suara
                                                </button>
                                            </form>

                                            <!-- Tombol Mulai Layanan -->
                                            <form action="{{ route('petugas.start', $activeQueue->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-500/30 transition-all">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Mulai Layanan
                                                </button>
                                            </form>

                                            <!-- Tombol Lewati -->
                                            <form action="{{ route('petugas.skip', $activeQueue->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-red-100 text-red-700 font-bold rounded-xl hover:bg-red-200 transition-all">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                                    Lewati
                                                </button>
                                            </form>
                                        @elseif($activeQueue->status === 'processing')
                                            <!-- Tombol Selesai -->
                                            <form action="{{ route('petugas.finish', $activeQueue->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-10 py-4 bg-indigo-600 text-white text-lg font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-500/40 transition-all transform hover:-translate-y-1">
                                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Selesaikan Pelayanan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center py-20 text-center">
                                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="text-xl text-gray-500 font-medium mb-8">Tidak ada antrian aktif saat ini.</p>
                                    
                                    <form action="{{ route('petugas.panggil') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-500/30 transition-all transform hover:scale-105">
                                            Panggil Antrian Selanjutnya
                                            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

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
                        @endif
                    </div>
                </div>

                <!-- Sidebar: Skipped/History -->
                <div class="space-y-8">
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
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
