<x-kos-layout title="Tagihan Bulanan">
    <x-slot name="actions">
        <form method="POST" action="{{ route('admin.payments.generate') }}"
              onsubmit="return confirm('Buat tagihan bulan ini untuk semua kontrak aktif yang belum punya tagihan?')">
            @csrf
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Generate Bulan Ini
            </button>
        </form>
        <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
            + Tagihan
        </a>
    </x-slot>

    <div class="rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="q" class="block text-sm font-medium text-gray-700">Cari</label>
                <input id="q" type="text" name="q" value="{{ $q }}" placeholder="Nama penghuni / periode"
                       class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Payment::STATUSES as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Filter</button>
            @if ($q !== '' || $status)
                <a href="{{ route('admin.payments.index') }}" class="py-2 text-sm text-gray-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Penghuni</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Nominal</th>
                    <th class="px-4 py-3">Bukti</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payments as $p)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->lease->user->name }}</td>
                        <td class="px-4 py-3">{{ $p->lease->room->nomor_kamar }}</td>
                        <td class="px-4 py-3">{{ $p->periode_bulan }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if ($p->bukti_url)
                                <a href="{{ $p->bukti_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Lihat</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><x-status-badge :status="$p->status" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($p->status === 'verifikasi')
                                <form method="POST" action="{{ route('admin.payments.verify', $p) }}" class="inline"
                                      onsubmit="return confirm('Tandai tagihan ini lunas?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-700 hover:underline">Verifikasi</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $p) }}" class="ml-3 inline"
                                      onsubmit="return confirm('Tolak bukti transfer? Penghuni harus mengunggah ulang.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-yellow-700 hover:underline">Tolak</button>
                                </form>
                            @endif

                            @if ($p->status !== 'lunas')
                                <a href="{{ route('admin.payments.edit', $p) }}" class="ml-3 text-indigo-600 hover:underline">Edit</a>
                            @endif

                            <form method="POST" action="{{ route('admin.payments.destroy', $p) }}" class="ml-3 inline"
                                  onsubmit="return confirm('Hapus tagihan ini? Tindakan tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $payments->links() }}
</x-kos-layout>
