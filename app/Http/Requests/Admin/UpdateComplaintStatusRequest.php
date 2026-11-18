<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateComplaintStatusRequest extends AdminRequest
{
    public function rules(): array
    {
        // Status awal "pending" tidak bisa dipilih ulang; alur hanya maju.
        return [
            'status' => ['required', Rule::in(['diproses', 'selesai'])],
        ];
    }
}
