<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit User: ' . $user->name) }}
    </x-slot>

    <div class="max-w-2xl bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-6">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="mb-6">
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div class="mb-6">
                <x-input-label for="role" value="Role Akses" />
                <select id="role" name="role" onchange="toggleCounter()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="pasien" {{ old('role', $user->role) === 'pasien' ? 'selected' : '' }}>Pasien</option>
                    <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('role')" />
            </div>

            <div id="counter_selection" class="mb-6 {{ old('role', $user->role) === 'petugas' ? '' : 'hidden' }}">
                <x-input-label for="counter_id" value="Tugaskan ke Loket" />
                <select id="counter_id" name="counter_id" {{ $counters->isEmpty() ? 'disabled' : '' }} class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">-- Pilih Loket --</option>
                    @foreach($counters as $counter)
                        <option value="{{ $counter->id }}" {{ old('counter_id', $user->counter_id) == $counter->id ? 'selected' : '' }}>
                            {{ $counter->name }} ({{ $counter->code }})
                        </option>
                    @endforeach
                </select>
                @if($counters->isEmpty())
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        Belum ada loket aktif. Silakan <a href="{{ route('admin.counters.create') }}" class="underline font-bold">tambah loket baru</a> terlebih dahulu.
                    </p>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('counter_id')" />
            </div>

            <script>
                function toggleCounter() {
                    const role = document.getElementById('role').value;
                    const selection = document.getElementById('counter_selection');
                    if (role === 'petugas') {
                        selection.classList.remove('hidden');
                    } else {
                        selection.classList.add('hidden');
                    }
                }
            </script>

            <div class="mb-6 border-t dark:border-gray-700 pt-6">
                <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-4">Ubah Password (Opsional)</h4>
                <div class="mb-4">
                    <x-input-label for="password" value="Password Baru" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
