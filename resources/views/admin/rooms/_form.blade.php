@php
    // Kamar yang punya kontrak aktif hanya boleh berstatus "terisi";
    // selain itu "terisi" tidak bisa dipilih manual (diatur lewat kontrak sewa).
    $terkunci = $room->exists && $room->activeLease()->exists();
    $opsiStatus = $terkunci ? ['terisi'] : ['tersedia', 'perbaikan'];
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-input-label for="nomor_kamar" value="Nomor kamar" />
        <x-text-input id="nomor_kamar" name="nomor_kamar" type="text" class="mt-1 block w-full"
                      :value="old('nomor_kamar', $room->nomor_kamar)" required />
        <x-input-error :messages="$errors->get('nomor_kamar')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tipe" value="Tipe" />
        <select id="tipe" name="tipe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach (\App\Models\Room::TIPE as $t)
                <option value="{{ $t }}" @selected(old('tipe', $room->tipe) === $t)>{{ $t }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tipe')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="harga_bulanan" value="Harga bulanan (Rp)" />
        <x-text-input id="harga_bulanan" name="harga_bulanan" type="number" min="0" step="1000" class="mt-1 block w-full"
                      :value="old('harga_bulanan', $room->harga_bulanan)" required />
        <x-input-error :messages="$errors->get('harga_bulanan')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach ($opsiStatus as $s)
                <option value="{{ $s }}" @selected(old('status', $room->status) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @if ($terkunci)
            <p class="mt-1 text-xs text-gray-500">Kamar masih disewa. Gunakan menu Kontrak Sewa → Checkout untuk mengosongkannya.</p>
        @endif
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="fasilitas" value="Fasilitas" />
        <textarea id="fasilitas" name="fasilitas" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('fasilitas', $room->fasilitas) }}</textarea>
        <x-input-error :messages="$errors->get('fasilitas')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
</div>
