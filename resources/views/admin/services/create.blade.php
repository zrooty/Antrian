<x-admin-layout>
    <x-slot name="header">
        {{ __('Tambah Layanan Baru') }}
    </x-slot>

    <div class="max-w-2xl bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <x-input-label for="nama_layanan" value="Nama Layanan" />
                <x-text-input id="nama_layanan" name="nama_layanan" type="text" class="mt-1 block w-full" :value="old('nama_layanan')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('nama_layanan')" />
            </div>

            <div class="mb-6">
                <x-input-label for="kode_prefix" value="Kode Prefix (Contoh: A, B, C)" />
                <x-text-input id="kode_prefix" name="kode_prefix" type="text" class="mt-1 block w-full uppercase" :value="old('kode_prefix')" required />
                <p class="mt-1 text-xs text-gray-500">Maksimal 5 karakter, digunakan sebagai awalan nomor antrian.</p>
                <x-input-error class="mt-2" :messages="$errors->get('kode_prefix')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Simpan Layanan') }}</x-primary-button>
                <a href="{{ route('admin.services.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
