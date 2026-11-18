<x-kos-layout title="Edit Keluhan">
    <div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('penghuni.complaints.update', $complaint) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('penghuni.complaints._form')
        </form>
    </div>
</x-kos-layout>
