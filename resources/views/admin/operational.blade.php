<x-admin-layout>
    <x-slot name="header">
        {{ __('Operasional Sistem') }}
    </x-slot>

    <div class="max-w-4xl">
        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-100 dark:border-green-800" role="alert">
                <span class="font-bold">Success!</span> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reset Antrian -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl w-fit mb-4">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Reset Antrian Hari Ini</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Aksi ini akan menghapus seluruh data antrian yang terdaftar pada hari ini. Aksi ini tidak dapat dibatalkan.</p>
                
                <form action="{{ route('admin.operational.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh antrian hari ini?')">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg shadow-red-600/20 active:scale-[0.98]">
                        Reset Antrian Sekarang
                    </button>
                </form>
            </div>

            <!-- Kontrol Pendaftaran -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm opacity-50 cursor-not-allowed">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl w-fit mb-4">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Tutup Pendaftaran</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Menghentikan pelanggan baru untuk mendaftar antrian secara sistem. Loket tetap dapat melayani antrian yang sudah ada.</p>
                
                <button disabled class="w-full py-3 px-4 bg-indigo-200 text-white font-bold rounded-xl cursor-not-allowed">
                    Fitur Segera Datang
                </button>
            </div>
        </div>
    </div>
</x-admin-layout>
