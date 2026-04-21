<x-admin-layout>
    <x-slot name="header">
        {{ __('Tambah Loket Baru') }}
    </x-slot>

    <div class="max-w-2xl bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
        <form action="{{ route('admin.counters.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <x-input-label for="name" value="Nama Loket" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="mb-6">
                <x-input-label for="code" value="Kode Loket (Contoh: L1, L2)" />
                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full uppercase" :value="old('code')" required />
                <x-input-error class="mt-2" :messages="$errors->get('code')" />
            </div>

            <div class="mb-6">
                <x-input-label for="status" value="Status" />
                <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Simpan Loket') }}</x-primary-button>
                <a href="{{ route('admin.counters.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
