<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanBakuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_bahan' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'in:gr,ml'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['nullable', 'numeric', 'min:0'],
            'tanggal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan baku wajib diisi.',
            'satuan.required' => 'Satuan bahan (gr atau ml) wajib dipilih.',
            'jumlah.required' => 'Jumlah stok wajib diisi.',
            'jumlah.min' => 'Jumlah stok minimal 0.01.',
            'harga_beli.required' => 'Total harga beli wajib diisi.',
            'harga_beli.min' => 'Total harga beli tidak boleh negatif.',
        ];
    }
}
