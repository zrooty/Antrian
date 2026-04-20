<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Petugas - Pemanggilan Antrian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg flex items-center justify-between border border-blue-100 dark:border-blue-800">
                        <div>
                            <h3 class="font-bold text-blue-800 dark:text-blue-300">Pengaturan Loket</h3>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Tentukan loket tempat Anda bertugas sekarang.</p>
                        </div>
                        <select id="loket-selector" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="1">Loket 1</option>
                            <option value="2">Loket 2</option>
                            <option value="3">Loket 3</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kolom Antrian Menunggu -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <span class="bg-blue-500 w-3 h-3 rounded-full mr-2"></span>
                                Daftar Antrian Menunggu
                            </h3>

                            @php
                                $waitingQueues = $queues->where('status', 'menunggu');
                            @endphp

                            @if($waitingQueues->isEmpty())
                                <p class="text-gray-500 italic">Tidak ada antrian yang menunggu.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($waitingQueues as $queue)
                                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600 flex justify-between items-center hover:shadow-md transition-shadow">
                                            <div>
                                                <div class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $queue->nomor_antrian }}</div>
                                                <div class="text-sm text-gray-500">{{ $queue->user->name }}</div>
                                            </div>
                                            <form action="{{ route('petugas.panggil', $queue->id) }}" method="POST">
                                                @csrf
                                                <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                                                    Panggil
                                                </x-primary-button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Kolom Sedang Dipanggil -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <span class="bg-green-500 w-3 h-3 rounded-full mr-2"></span>
                                Sedang Dipanggil / Aktif
                            </h3>

                            @php
                                $calledQueues = $queues->where('status', 'dipanggil');
                            @endphp

                            @if($calledQueues->isEmpty())
                                <p class="text-gray-500 italic">Tidak ada nomor yang sedang dipanggil.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($calledQueues as $queue)
                                        <div class="bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 p-4 rounded-lg flex justify-between items-center">
                                            <div>
                                                <div class="text-2xl font-black text-green-700 dark:text-green-400">{{ $queue->nomor_antrian }}</div>
                                                <div class="text-sm text-gray-600 dark:text-green-300/70">{{ $queue->user->name }}</div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <form action="{{ route('petugas.panggil', $queue->id) }}" method="POST">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="text-xs">
                                                        Panggil Ulang
                                                    </x-secondary-button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selector = document.getElementById('loket-selector');
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', () => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'loket';
                    input.value = selector.value;
                    form.appendChild(input);
                });
            });
        });
    </script>
</x-app-layout>
