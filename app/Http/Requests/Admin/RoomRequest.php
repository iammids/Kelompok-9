<?php

namespace App\Http\Requests\Admin;

use App\Models\Room;
use Illuminate\Validation\Rule;

/** Dipakai untuk store & update kamar (unique mengabaikan kamar yang sedang diedit). */
class RoomRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'nomor_kamar' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms', 'nomor_kamar')->ignore($this->route('room')),
            ],
            'tipe' => ['required', Rule::in(Room::TIPE)],
            'harga_bulanan' => ['required', 'integer', 'min:0', 'max:100000000'],
            'fasilitas' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(Room::STATUSES)],
        ];
    }
}
