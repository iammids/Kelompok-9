<x-kos-layout title="Edit Kontrak Sewa">
    <div class="rounded-lg bg-white p-6 shadow">
        <dl class="mb-6 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
            <div><dt class="text-gray-500">Penghuni</dt><dd class="font-medium text-gray-900">{{ $lease->user->name }}</dd></div>
            <div><dt class="text-gray-500">Kamar</dt><dd class="font-medium text-gray-900">{{ $lease->room->nomor_kamar }} ({{ $lease->room->tipe }})</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$lease->status" /></dd></div>
        </dl>

        <form method="POST" action="{{ route('admin.leases.update', $lease) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="tanggal_masuk" value="Tanggal masuk" />
                <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="mt-1 block w-full"
                              :value="old('tanggal_masuk', $lease->tanggal_masuk->toDateString())" required />
                <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="tanggal_keluar" value="Tanggal keluar (opsional)" />
                <x-text-input id="tanggal_keluar" name="tanggal_keluar" type="date" class="mt-1 block w-full"
                              :value="old('tanggal_keluar', $lease->tanggal_keluar?->toDateString())" />
                <p class="mt-1 text-xs text-gray-500">Untuk mengakhiri sewa dan mengosongkan kamar, gunakan tombol Checkout di daftar kontrak.</p>
                <x-input-error :messages="$errors->get('tanggal_keluar')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan</x-primary-button>
                <a href="{{ route('admin.leases.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-kos-layout>
