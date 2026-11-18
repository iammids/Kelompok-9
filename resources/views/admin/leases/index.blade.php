<x-kos-layout title="Kontrak Sewa">
    <x-slot name="actions">
        <a href="{{ route('admin.leases.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
            + Kontrak Baru
        </a>
    </x-slot>

    <div class="rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('admin.leases.index') }}" class="flex items-end gap-3">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                </select>
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Penghuni</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Tanggal masuk</th>
                    <th class="px-4 py-3">Tanggal keluar</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($leases as $lease)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $lease->user->name }}</td>
                        <td class="px-4 py-3">{{ $lease->room->nomor_kamar }} <span class="text-gray-400">({{ $lease->room->tipe }})</span></td>
                        <td class="px-4 py-3">{{ $lease->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $lease->tanggal_keluar?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$lease->status" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('admin.leases.edit', $lease) }}" class="text-indigo-600 hover:underline">Edit</a>

                            @if ($lease->status === 'aktif')
                                <form method="POST" action="{{ route('admin.leases.checkout', $lease) }}" class="ml-3 inline"
                                      onsubmit="return confirm('Akhiri sewa ini? Kamar akan kembali tersedia.')">
                                    @csrf
                                    <button type="submit" class="text-yellow-700 hover:underline">Checkout</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.leases.destroy', $lease) }}" class="ml-3 inline"
                                  onsubmit="return confirm('Hapus kontrak ini beserta seluruh tagihannya? Tindakan tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada kontrak sewa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $leases->links() }}
</x-kos-layout>
