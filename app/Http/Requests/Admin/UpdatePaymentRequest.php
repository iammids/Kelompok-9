<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends AdminRequest
{
    public function rules(): array
    {
        $payment = $this->route('payment');

        return [
            'periode_bulan' => [
                'required', 'string', 'max:50',
                Rule::unique('payments', 'periode_bulan')
                    ->where('lease_id', $payment?->lease_id)
                    ->ignore($payment),
            ],
            'nominal' => ['required', 'integer', 'min:0', 'max:100000000'],
        ];
    }
}
