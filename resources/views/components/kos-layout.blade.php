@props(['title'])

{{-- Pembungkus halaman: header judul (+ tombol aksi opsional), flash message, dan konten. --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            <x-flash />
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
