<?php

namespace App\Http\Requests\Penghuni;

use Illuminate\Foundation\Http\FormRequest;

/** Basis Form Request untuk aksi Penghuni: hanya role penghuni yang boleh. */
abstract class PenghuniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isPenghuni();
    }
}
