@props(['status'])

@php
    $warna = [
        'tersedia' => 'bg-green-100 text-green-800',
        'terisi' => 'bg-blue-100 text-blue-800',
        'perbaikan' => 'bg-yellow-100 text-yellow-800',
        'aktif' => 'bg-green-100 text-green-800',
        'selesai' => 'bg-gray-100 text-gray-700',
        'belum_bayar' => 'bg-red-100 text-red-800',
        'verifikasi' => 'bg-yellow-100 text-yellow-800',
        'lunas' => 'bg-green-100 text-green-800',
        'pending' => 'bg-red-100 text-red-800',
        'diproses' => 'bg-yellow-100 text-yellow-800',
    ];
    $kelas = $warna[$status] ?? 'bg-gray-100 text-gray-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize '.$kelas]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
