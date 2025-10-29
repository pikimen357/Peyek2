<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:100',
            'desa' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'catatan' => 'nullable|string',
            'payment_method' => 'required|in:cash,qris'
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi',
            'telepon.required' => 'Telepon harus diisi',
            'kecamatan.required' => 'Kecamatan harus dipilih',
            'desa.required' => 'Desa harus dipilih',
            'alamat.string' => 'Alamat harus berupa teks',
            'catatan.string' => 'Catatan harus berupa teks',

            'payment_method.required' => 'Metode pembayaran harus dipilih',
            'payment_method.in' => 'Metode pembayaran tidak valid',
        ];
    }

}
