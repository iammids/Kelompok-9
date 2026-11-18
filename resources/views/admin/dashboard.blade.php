<x-kos-layout title="Dashboard Admin">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Kamar tersedia / terisi</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">
                <span class="text-green-600">{{ $kamarTersedia }}</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600">{{ $kamarTerisi }}</span>
            </p>
            @if ($kamarPerbaikan > 0)
                <p class="mt-1 text-xs text-yellow-700">{{ $kamarPerbaikan }} kamar dalam perbaikan</p>
            @endif
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Pemasukan {{ $periode }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-400">Tagihan lunas periode berjalan</p>
        </div>

        <a href="{{ route('admin.payments.index', ['status' => 'verifikasi']) }}" class="rounded-lg bg-white p-5 shadow hover:bg-gray-50">
            <p class="text-sm text-gray-500">Menunggu verifikasi</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $menungguVerifikasi }}</p>
            <p class="mt-1 text-xs text-gray-400">Tagihan dengan bukti transfer</p>
        </a>

        <a href="{{ route('admin.complaints.index') }}" class="rounded-lg bg-white p-5 shadow hover:bg-gray-50">
            <p class="text-sm text-gray-500">Keluhan aktif</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ $keluhanAktif }}</p>
            <p class="mt-1 text-xs text-gray-400">Pending + diproses</p>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow">
            <h3 class="mb-3 font-semibold text-gray-800">Tagihan menunggu verifikasi</h3>
            @forelse ($tagihanVerifikasi as $p)
                <div class="flex items-center justify-between border-t py-2 text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $p->lease->user->name }} · Kamar {{ $p->lease->room->nomor_kamar }}</p>
                        <p class="text-gray-500">{{ $p->periode_bulan }} · Rp {{ number_format($p->nominal, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('admin.payments.index', ['status' => 'verifikasi']) }}" class="text-indigo-600 hover:underline">Periksa</a>
                </div>
            @empty
                <p class="text-sm text-gray-500">Tidak ada tagihan yang menunggu verifikasi.</p>
            @endforelse
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <h3 class="mb-3 font-semibold text-gray-800">Keluhan aktif terbaru</h3>
            @forelse ($keluhanTerbaru as $c)
                <div class="flex items-center justify-between border-t py-2 text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $c->judul }}</p>
                        <p class="text-gray-500">{{ $c->user->name }} · Kamar {{ $c->room->nomor_kamar }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$c->status" />
                        <a href="{{ route('admin.complaints.show', $c) }}" class="text-indigo-600 hover:underline">Detail</a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Tidak ada keluhan aktif.</p>
            @endforelse
        </div>
    </div>
</x-kos-layout>
