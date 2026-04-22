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
                @include('dashboard.partials.no-queue')
            @endif

            {{-- KONDISI B: ADA ANTRIAN AKTIF --}}
            @if($activeQueue)
                @include('dashboard.partials.active-queue')
            @endif

            {{-- KONDISI C: ANTRIAN SUDAH SELESAI HARI INI (Tapi tidak ada antrian aktif) --}}
            @if($doneQueue && !$activeQueue)
                @include('dashboard.partials.done-queue')
            @endif

            <!-- Riwayat Kunjungan -->
            @include('dashboard.partials.history-table')

        </div>
    </div>

    @if($activeQueue && in_array($activeQueue->status, ['waiting', 'called', 'processing', 'skipped']))
        @include('dashboard.partials.scripts')
    @endif
</x-app-layout>
