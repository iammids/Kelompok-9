<x-kos-layout title="Detail Keluhan">
    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->judul }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $complaint->user->name }} · Kamar {{ $complaint->room->nomor_kamar }} · {{ $complaint->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <x-status-badge :status="$complaint->status" />
        </div>

        <p class="mt-4 whitespace-pre-line text-sm text-gray-700">{{ $complaint->deskripsi }}</p>

        @if ($complaint->foto_url)
            <a href="{{ $complaint->foto_url }}" target="_blank" rel="noopener" class="mt-4 inline-block">
                <img src="{{ $complaint->foto_url }}" alt="Foto kerusakan" class="max-h-72 rounded-md border">
            </a>
        @endif

        <div class="mt-6 flex items-center gap-3 border-t pt-4">
            @if ($complaint->status === 'pending')
                <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="diproses">
                    <x-primary-button>Tandai Diproses</x-primary-button>
                </form>
            @elseif ($complaint->status === 'diproses')
                <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}"
                      onsubmit="return confirm('Tandai keluhan ini selesai?')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="selesai">
                    <x-primary-button>Tandai Selesai</x-primary-button>
                </form>
            @else
                <p class="text-sm text-gray-500">Keluhan ini sudah selesai ditangani.</p>
            @endif

            <a href="{{ route('admin.complaints.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
        </div>
    </div>
</x-kos-layout>
