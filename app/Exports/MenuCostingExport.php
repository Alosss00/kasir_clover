<?php

namespace App\Exports;

use App\Models\Menu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MenuCostingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Menu::with('resep.bahanBaku')->orderBy('nama_menu', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Menu',
            'Kategori',
            'Daftar Resep & Takaran Bahan',
            'Biaya Lain / Cup (Rp)',
            'Total Cost per Cup / HPP (Rp)',
            'Harga Jual (Rp)',
            'Keuntungan / Cup (Rp)',
            'Margin (%)',
            'Status Aktif',
        ];
    }

    public function map($menu): array
    {
        $resepString = $menu->resep->map(function ($r) {
            $nama = $r->bahanBaku ? $r->bahanBaku->nama_bahan : 'Bahan #'.$r->bahan_baku_id;
            $satuan = $r->bahanBaku ? $r->bahanBaku->satuan : '';
            return "{$nama} ({$r->jumlah_pemakaian} {$satuan})";
        })->implode(', ');

        return [
            $menu->id,
            $menu->nama_menu,
            $menu->kategori,
            $resepString ?: 'Tanpa Resep',
            (float) $menu->biaya_lain,
            (float) $menu->cost_per_cup,
            (float) $menu->harga_jual,
            (float) $menu->keuntungan_per_cup,
            $menu->margin_persen . '%',
            $menu->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']]],
        ];
    }
}
