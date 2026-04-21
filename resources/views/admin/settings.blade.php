<x-admin-layout>
    <x-slot name="header">
        {{ __('Pengaturan Sistem') }}
    </x-slot>

    <div class="max-w-4xl bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-100 dark:border-green-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="space-y-8">
                <!-- Identitas Aplikasi -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Identitas Aplikasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="app_name" value="Nama Aplikasi" />
                            <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full" :value="\App\Models\Setting::where('key', 'app_name')->first()?->value ?? config('app.name')" />
                        </div>
                        <div>
                            <x-input-label for="app_slogan" value="Slogan / Deskripsi Singkat" />
                            <x-text-input id="app_slogan" name="app_slogan" type="text" class="mt-1 block w-full" :value="\App\Models\Setting::where('key', 'app_slogan')->first()?->value ?? 'Sistem Antrian Modern'" />
                        </div>
                    </div>
                </section>

                <!-- Kebijakan Operasional -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Kebijakan Operasional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="max_queue_per_day" value="Maksimal Antrian per Hari (0 = Tanpa Batas)" />
                            <x-text-input id="max_queue_per_day" name="max_queue_per_day" type="number" class="mt-1 block w-full" :value="\App\Models\Setting::where('key', 'max_queue_per_day')->first()?->value ?? 0" />
                        </div>
                        <div>
                            <x-input-label for="queue_prefix_mode" value="Mode Prefix Antrian" />
                            <select id="queue_prefix_mode" name="queue_prefix_mode" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="per_service" {{ (\App\Models\Setting::where('key', 'queue_prefix_mode')->first()?->value ?? 'per_service') === 'per_service' ? 'selected' : '' }}>Berdasarkan Layanan (A-001, B-001)</option>
                                <option value="global" {{ (\App\Models\Setting::where('key', 'queue_prefix_mode')->first()?->value === 'global') ? 'selected' : '' }}>Global (001, 002)</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end">
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>
