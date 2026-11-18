<?php

namespace App\Http\Requests\Penghuni;

class UploadProofRequest extends PenghuniRequest
{
    public function rules(): array
    {
        return [
            // mimes memeriksa isi file (bukan hanya ekstensi); batas 2 MB.
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['bukti' => 'bukti transfer'];
    }
}
