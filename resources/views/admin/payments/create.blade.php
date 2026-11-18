<x-kos-layout title="Tambah Tagihan">
    <div class="rounded-lg bg-white p-6 shadow">
        @if ($leases->isEmpty())
            <p class="text-sm text-gray-600">Belum ada kontrak aktif. Buat kontrak sewa terlebih dahulu.</p>
            <a href="{{ route('admin.payments.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">← Kembali</a>
        @else
            {{-- Alpine: pilih kontrak => nominal terisi otomatis dari harga kamar (tetap bisa diubah) --}}
            <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-5"
                  x-data="{ nominal: @js(old('nominal', '')) }">
                @csrf

                <div>
                    <x-input-label for="lease_id" value="Kontrak aktif" />
                    <select id="lease_id" name="lease_id"
                            @change="nominal = $event.target.selectedOptions[0].dataset.harga || ''"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">— Pilih kontrak —</option>
                        @foreach ($leases as $l)
                            <option value="{{ $l->id }}" data-harga="{{ $l->room->harga_bulanan }}" @selected((int) old('lease_id') === $l->id)>
                                Kamar {{ $l->room->nomor_kamar }} · {{ $l->user->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('lease_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="periode_bulan" value="Periode bulan" />
                    <x-text-input id="periode_bulan" name="periode_bulan" type="text" class="mt-1 block w-full"
                                  :value="old('periode_bulan', $periodeDefault)" placeholder="mis. November 2026" required />
                    <x-input-error :messages="$errors->get('periode_bulan')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nominal" value="Nominal (Rp)" />
                    <input id="nominal" name="nominal" type="number" min="0" step="1000" x-model="nominal" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button>Simpan</x-primary-button>
                    <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        @endif
    </div>
</x-kos-layout>
