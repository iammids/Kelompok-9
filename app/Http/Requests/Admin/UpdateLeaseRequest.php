<?php

namespace App\Http\Requests\Admin;

class UpdateLeaseRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_keluar' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
        ];
    }
}
