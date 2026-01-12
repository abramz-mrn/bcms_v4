<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('customer')?->id ?? null;

        return [
            'code' => ['sometimes','string','max:50',"unique:customers,code,$id"],
            'name' => ['sometimes','string','max:200'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:200'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string','max:100'],
            'state' => ['nullable','string','max:100'],
            'pos' => ['nullable','string','max:20'],
            'group_area' => ['nullable','string','max:100'],
            'notes' => ['nullable','string'],
        ];
    }
}