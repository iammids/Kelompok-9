<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class StorePaymentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'lease_id' => ['required', Rule::exists('leases', 'id')->where('status', 'aktif')],
            'periode_bulan' => [
                'required', 'string', 'max:50',
                // satu kontrak hanya boleh punya satu tagihan per periode
                Rule::unique('payments', 'periode_bulan')
                    ->where('lease_id', $this->input('lease_id')),
            ],
            'nominal' => ['required', 'integer', 'min:0', 'max:100000000'],
        ];
    }
}
