<x-kos-layout title="Data Kamar">
    <x-slot name="actions">
        <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
            + Tambah Kamar
        </a>
    </x-slot>

    <div class="rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('admin.rooms.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="q" class="block text-sm font-medium text-gray-700">Cari</label>
                <input id="q" type="text" name="q" value="{{ $q }}" placeholder="Nomor atau tipe kamar"
                       class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Room::STATUSES as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Filter</button>
            @if ($q !== '' || $status)
                <a href="{{ route('admin.rooms.index') }}" class="py-2 text-sm text-gray-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">No. Kamar</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Harga / bulan</th>
                    <th class="px-4 py-3">Fasilitas</th>
                    <th class="px-4 py-3">Penghuni</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rooms as $room)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $room->nomor_kamar }}</td>
                        <td class="px-4 py-3">{{ $room->tipe }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($room->harga_bulanan, 0, ',', '.') }}</td>
                        <td class="max-w-xs px-4 py-3 text-gray-500">{{ \Illuminate\Support\Str::limit($room->fasilitas, 60) ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $room->activeLease?->user->name ?? '-' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$room->status" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="ml-3 inline"
                                  onsubmit="return confirm('Yakin menghapus kamar ini? Tindakan tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data kamar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $rooms->links() }}
</x-kos-layout>
