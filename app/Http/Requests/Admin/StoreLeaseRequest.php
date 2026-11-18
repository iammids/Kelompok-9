<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class StoreLeaseRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'penghuni')],
            'room_id' => ['required', 'exists:rooms,id'],
            'tanggal_masuk' => ['required', 'date'],
        ];
    }
}
