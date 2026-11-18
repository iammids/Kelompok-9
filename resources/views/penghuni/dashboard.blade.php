<x-kos-layout title="Dashboard Penghuni">
    @if (! $lease)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-5 text-sm text-yellow-800">
            Anda belum memiliki kontrak sewa aktif. Hubungi pemilik kos untuk dibuatkan kontrak.
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-lg bg-white p-5 shadow">
                <h3 class="mb-3 font-semibold text-gray-800">Kamar yang ditempati</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500">Nomor kamar</dt><dd class="text-lg font-bold text-gray-900">{{ $lease->room->nomor_kamar }}</dd></div>
                    <div><dt class="text-gray-500">Tipe</dt><dd class="font-medium text-gray-900">{{ $lease->room->tipe }}</dd></div>
                    <div><dt class="text-gray-500">Harga / bulan</dt><dd class="font-medium text-gray-900">Rp {{ number_format($lease->room->harga_bulanan, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-gray-500">Masuk sejak</dt><dd class="font-medium text-gray-900">{{ $lease->tanggal_masuk->format('d M Y') }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Fasilitas</dt><dd class="text-gray-700">{{ $lease->room->fasilitas ?: '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg bg-white p-5 shadow">
                <h3 class="mb-3 font-semibold text-gray-800">Tagihan {{ $periode }}</h3>
                @if ($tagihanBulanIni)
                    <div class="flex items-center justify-between">
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($tagihanBulanIni->nominal, 0, ',', '.') }}</p>
                        <x-status-badge :status="$tagihanBulanIni->status" />
                    </div>
                    @if ($tagihanBulanIni->status === 'belum_bayar')
                        <a href="{{ route('penghuni.payments.index') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                            Unggah bukti transfer
                        </a>
                    @elseif ($tagihanBulanIni->status === 'verifikasi')
                        <p class="mt-3 text-sm text-gray-500">Bukti transfer sedang diverifikasi oleh pemilik kos.</p>
                    @else
                        <p class="mt-3 text-sm text-green-700">Terima kasih, tagihan bulan ini sudah lunas.</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Belum ada tagihan untuk periode ini.</p>
                @endif
            </div>
        </div>
    @endif

    <div class="rounded-lg bg-white p-5 shadow">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Keluhan saya</h3>
            <a href="{{ route('penghuni.complaints.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
        </div>

        @forelse ($keluhan as $c)
            <div class="flex items-center justify-between border-t py-2 text-sm">
                <div>
                    <p class="font-medium text-gray-800">{{ $c->judul }}</p>
                    <p class="text-gray-500">Kamar {{ $c->room->nomor_kamar }} · {{ $c->created_at->format('d M Y') }}</p>
                </div>
                <x-status-badge :status="$c->status" />
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada keluhan.</p>
        @endforelse
    </div>
</x-kos-layout>
