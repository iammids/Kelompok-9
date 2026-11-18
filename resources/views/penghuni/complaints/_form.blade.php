<div class="space-y-5">
    <div>
        <x-input-label for="judul" value="Judul keluhan" />
        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full"
                      :value="old('judul', $complaint->judul)" required />
        <x-input-error :messages="$errors->get('judul')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="deskripsi" value="Deskripsi kerusakan" />
        <textarea id="deskripsi" name="deskripsi" rows="5" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $complaint->deskripsi) }}</textarea>
        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="foto" value="Foto kerusakan (opsional)" />
        @if ($complaint->foto_url)
            <img src="{{ $complaint->foto_url }}" alt="Foto saat ini" class="mt-2 max-h-40 rounded-md border">
            <p class="mt-1 text-xs text-gray-500">Pilih file baru untuk mengganti foto di atas.</p>
        @endif
        <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png"
               class="mt-2 block w-full text-sm text-gray-600 file:mr-3 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-1.5">
        <p class="mt-1 text-xs text-gray-500">JPG atau PNG, maksimal 2 MB.</p>
        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('penghuni.complaints.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>
