<x-kos-layout title="Edit Tagihan">
    <div class="rounded-lg bg-white p-6 shadow">
        <dl class="mb-6 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
            <div><dt class="text-gray-500">Penghuni</dt><dd class="font-medium text-gray-900">{{ $payment->lease->user->name }}</dd></div>
            <div><dt class="text-gray-500">Kamar</dt><dd class="font-medium text-gray-900">{{ $payment->lease->room->nomor_kamar }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$payment->status" /></dd></div>
        </dl>

        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="periode_bulan" value="Periode bulan" />
                <x-text-input id="periode_bulan" name="periode_bulan" type="text" class="mt-1 block w-full"
                              :value="old('periode_bulan', $payment->periode_bulan)" required />
                <x-input-error :messages="$errors->get('periode_bulan')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="nominal" value="Nominal (Rp)" />
                <x-text-input id="nominal" name="nominal" type="number" min="0" step="1000" class="mt-1 block w-full"
                              :value="old('nominal', $payment->nominal)" required />
                <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan</x-primary-button>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-kos-layout>
