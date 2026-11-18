<x-kos-layout title="Buat Kontrak Sewa">
    <div class="rounded-lg bg-white p-6 shadow">
        @if ($rooms->isEmpty() || $users->isEmpty())
            <p class="text-sm text-gray-600">
                @if ($rooms->isEmpty())
                    Tidak ada kamar berstatus <strong>tersedia</strong>.
                @endif
                @if ($users->isEmpty())
                    Tidak ada penghuni tanpa kontrak aktif (daftarkan akun penghuni baru lewat halaman registrasi).
                @endif
            </p>
            <a href="{{ route('admin.leases.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">← Kembali</a>
        @else
            <form method="POST" action="{{ route('admin.leases.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="user_id" value="Penghuni" />
                    <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">— Pilih penghuni —</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected((int) old('user_id') === $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="room_id" value="Kamar (hanya yang tersedia)" />
                    <select id="room_id" name="room_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">— Pilih kamar —</option>
                        @foreach ($rooms as $r)
                            <option value="{{ $r->id }}" @selected((int) old('room_id') === $r->id)>
                                {{ $r->nomor_kamar }} · {{ $r->tipe }} · Rp {{ number_format($r->harga_bulanan, 0, ',', '.') }}/bulan
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tanggal_masuk" value="Tanggal masuk" />
                    <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="mt-1 block w-full"
                                  :value="old('tanggal_masuk', now()->toDateString())" required />
                    <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button>Buat Kontrak</x-primary-button>
                    <a href="{{ route('admin.leases.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        @endif
    </div>
</x-kos-layout>
