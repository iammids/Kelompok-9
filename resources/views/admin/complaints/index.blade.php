<x-kos-layout title="Keluhan Fasilitas">
    <div class="rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('admin.complaints.index') }}" class="flex items-end gap-3">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Complaint::STATUSES as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Penghuni</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($complaints as $c)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $c->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $c->user->name }}</td>
                        <td class="px-4 py-3">{{ $c->room->nomor_kamar }}</td>
                        <td class="px-4 py-3">{{ $c->judul }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$c->status" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('admin.complaints.show', $c) }}" class="text-indigo-600 hover:underline">Detail</a>

                            @if ($c->status === 'pending')
                                <form method="POST" action="{{ route('admin.complaints.update', $c) }}" class="ml-3 inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="diproses">
                                    <button type="submit" class="text-yellow-700 hover:underline">Proses</button>
                                </form>
                            @elseif ($c->status === 'diproses')
                                <form method="POST" action="{{ route('admin.complaints.update', $c) }}" class="ml-3 inline"
                                      onsubmit="return confirm('Tandai keluhan ini selesai?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit" class="text-green-700 hover:underline">Selesaikan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada keluhan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $complaints->links() }}
</x-kos-layout>
