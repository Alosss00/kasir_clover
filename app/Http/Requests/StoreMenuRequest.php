<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nama_menu' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'biaya_lain' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'resep' => ['nullable', 'array'],
            'resep.*.bahan_baku_id' => ['required_with:resep', 'exists:bahan_baku,id'],
            'resep.*.jumlah_pemakaian' => ['required_with:resep', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'kategori.required' => 'Kategori menu wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.min' => 'Harga jual tidak boleh bernilai negatif.',
        ];
    }
}
