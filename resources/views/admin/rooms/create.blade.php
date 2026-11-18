<x-kos-layout title="Tambah Kamar">
    <div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            @include('admin.rooms._form')
        </form>
    </div>
</x-kos-layout>
