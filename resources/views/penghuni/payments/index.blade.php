<x-kos-layout title="Tagihan Saya">
    @error('bukti')
        <div role="alert" class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
    @enderror

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Nominal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Bukti</th>
                    <th class="px-4 py-3">Unggah bukti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payments as $p)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->periode_bulan }}</td>
                        <td class="px-4 py-3">{{ $p->lease->room->nomor_kamar }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$p->status" /></td>
                        <td class="px-4 py-3">
                            @if ($p->bukti_url)
                                <a href="{{ $p->bukti_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Lihat</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($p->status === 'belum_bayar')
                                <form method="POST" action="{{ route('penghuni.payments.upload', $p) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required
                                           class="block w-48 text-xs text-gray-600 file:mr-2 file:rounded file:border-0 file:bg-gray-100 file:px-2 file:py-1">
                                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">Kirim</button>
                                </form>
                            @elseif ($p->status === 'verifikasi')
                                <span class="text-gray-500">Menunggu verifikasi</span>
                            @else
                                <span class="text-green-700">Lunas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-500">Format bukti: JPG, PNG, atau PDF (maks. 2 MB).</p>

    {{ $payments->links() }}
</x-kos-layout>
