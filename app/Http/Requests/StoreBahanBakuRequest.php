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
            'mode' => ['nullable', 'in:existing,new'],
            'bahan_baku_id' => ['nullable', 'required_if:mode,existing', 'exists:bahan_baku,id'],
            'nama_bahan' => ['nullable', 'required_if:mode,new', 'string', 'max:255'],
            'satuan' => ['nullable', 'required_if:mode,new', 'in:gr,ml'],
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
            'bahan_baku_id.required_if' => 'Pilih bahan baku yang ingin di-restock.',
            'bahan_baku_id.exists' => 'Bahan baku yang dipilih tidak valid.',
            'nama_bahan.required_if' => 'Nama bahan baku baru wajib diisi.',
            'satuan.required_if' => 'Satuan bahan (gr atau ml) wajib dipilih.',
            'jumlah.required' => 'Jumlah stok wajib diisi.',
            'jumlah.min' => 'Jumlah stok minimal 0.01.',
            'harga_beli.required' => 'Total harga beli wajib diisi.',
            'harga_beli.min' => 'Total harga beli tidak boleh bernilai negatif.',
        ];
    }
}
