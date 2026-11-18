<x-kos-layout :title="'Edit Kamar '.$room->nomor_kamar">
    <div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
            @csrf
            @method('PUT')
            @include('admin.rooms._form')
        </form>
    </div>
</x-kos-layout>
