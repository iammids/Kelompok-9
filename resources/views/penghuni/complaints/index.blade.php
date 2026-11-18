<x-kos-layout title="Keluhan Saya">
    <x-slot name="actions">
        <a href="{{ route('penghuni.complaints.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
            + Buat Keluhan
        </a>
    </x-slot>

    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kamar</th>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Foto</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($complaints as $c)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $c->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $c->room->nomor_kamar }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $c->judul }}</p>
                            <p class="text-gray-500">{{ \Illuminate\Support\Str::limit($c->deskripsi, 70) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if ($c->foto_url)
                                <a href="{{ $c->foto_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Lihat</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><x-status-badge :status="$c->status" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($c->status === 'pending')
                                <a href="{{ route('penghuni.complaints.edit', $c) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('penghuni.complaints.destroy', $c) }}" class="ml-3 inline"
                                      onsubmit="return confirm('Batalkan dan hapus keluhan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-400">Dikunci</span>
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
