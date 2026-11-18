<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Basis Form Request untuk semua aksi Admin: hanya role admin yang boleh. */
abstract class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function attributes(): array
    {
        return [
            'nomor_kamar' => 'nomor kamar',
            'harga_bulanan' => 'harga bulanan',
            'user_id' => 'penghuni',
            'room_id' => 'kamar',
            'lease_id' => 'kontrak',
            'tanggal_masuk' => 'tanggal masuk',
            'tanggal_keluar' => 'tanggal keluar',
            'periode_bulan' => 'periode bulan',
        ];
    }
}
