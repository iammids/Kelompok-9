<x-kos-layout title="Buat Keluhan">
    <div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('penghuni.complaints.store') }}" enctype="multipart/form-data">
            @csrf
            @include('penghuni.complaints._form')
        </form>
    </div>
</x-kos-layout>
