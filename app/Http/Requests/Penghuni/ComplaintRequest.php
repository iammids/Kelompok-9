<?php

namespace App\Http\Requests\Penghuni;

/** Dipakai untuk store & update keluhan. */
class ComplaintRequest extends PenghuniRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'judul' => 'judul keluhan',
            'deskripsi' => 'deskripsi',
            'foto' => 'foto kerusakan',
        ];
    }
}
