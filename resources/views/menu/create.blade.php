<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Buat Menu Baru & Resep HPP</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sistem akan menghitung otomatis modal HPP dan estimasi keuntungan</p>
            </div>
            <a href="{{ route('menu.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs flex items-center gap-1">
                <span>← Kembali</span>
            </a>
        </div>
    </x-slot>

    <!-- Alpine Data for Dynamic Recipe Builder & Live Cost Simulator -->
    <div 
        x-data="{
            namaMenu: '',
            kategori: 'Coffee',
            hargaJual: 25000,
            biayaLain: 4000,
            bahanOptions: {{ Js::from($bahanBakuList) }},
            resepRows: [
                { bahan_baku_id: '{{ $bahanBakuList->first()?->id }}', jumlah_pemakaian: 18 }
            ],
            addRow() {
                this.resepRows.push({
                    bahan_baku_id: this.bahanOptions.length > 0 ? this.bahanOptions[0].id : '',
                    jumlah_pemakaian: 10
                });
            },
            removeRow(index) {
                if (this.resepRows.length > 1) {
                    this.resepRows.splice(index, 1);
                }
            },
            getBahan(id) {
                return this.bahanOptions.find(b => b.id == id);
            },
            calculateRowCost(row) {
                let b = this.getBahan(row.bahan_baku_id);
                if (!b) return 0;
                return (parseFloat(row.jumlah_pemakaian) || 0) * parseFloat(b.harga_per_satuan);
            },
            totalCostBahan() {
                return this.resepRows.reduce((acc, row) => acc + this.calculateRowCost(row), 0);
            },
            totalCostPerCup() {
                return this.totalCostBahan() + (parseFloat(this.biayaLain) || 0);
            },
            keuntungan() {
                return (parseFloat(this.hargaJual) || 0) - this.totalCostPerCup();
            },
            marginPersen() {
                let jual = parseFloat(this.hargaJual) || 0;
                if (jual <= 0) return 0;
                return ((this.keuntungan() / jual) * 100).toFixed(1);
            }
        }" 
        class="space-y-5"
    >
        <form method="POST" action="{{ route('menu.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Kiri: Form & Bahan Resep -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Info Dasar Menu -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2.5">
                            1. Informasi Menu
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Menu</label>
                                <input 
                                    type="text" 
                                    name="nama_menu" 
                                    x-model="namaMenu" 
                                    required 
                                    placeholder="Contoh: Kopi Susu Aren" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Kategori Menu</label>
                                <select 
                                    name="kategori" 
                                    x-model="kategori" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                                >
                                    <option value="Coffee">Coffee</option>
                                    <option value="Non-Coffee">Non-Coffee</option>
                                    <option value="Tea & Herbal">Tea & Herbal</option>
                                    <option value="Food & Snack">Food & Snack</option>
                                    <option value="Bakery">Bakery</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Harga Jual ke Pelanggan (Rp)</label>
                                <input 
                                    type="number" 
                                    name="harga_jual" 
                                    x-model.number="hargaJual" 
                                    required 
                                    min="0" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-emerald-700 font-bold text-base focus:bg-white focus:border-emerald-600 outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Biaya Cup / Kemasan (Rp)</label>
                                <input 
                                    type="number" 
                                    name="biaya_lain" 
                                    x-model.number="biayaLain" 
                                    min="0" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Racik Bahan Resep -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <h3 class="font-bold text-slate-900 text-sm">
                                2. Takaran Resep Bahan Baku
                            </h3>

                            <button 
                                type="button" 
                                @click="addRow()" 
                                class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-all"
                            >
                                + Tambah Bahan
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <template x-for="(row, index) in resepRows" :key="index">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center gap-2.5">
                                    
                                    <!-- Pilih Bahan -->
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Pilih Bahan Baku</label>
                                        <select 
                                            :name="`resep[${index}][bahan_baku_id]`" 
                                            x-model="row.bahan_baku_id" 
                                            class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-slate-800 text-xs focus:border-emerald-600 outline-none"
                                        >
                                            <template x-for="b in bahanOptions" :key="b.id">
                                                <option :value="b.id" x-text="`${b.nama_bahan} (Rp ${parseFloat(b.harga_per_satuan).toLocaleString('id-ID')}/${b.satuan})`"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Takaran -->
                                    <div class="w-full sm:w-32">
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1">
                                            Takaran (<span x-text="getBahan(row.bahan_baku_id)?.satuan || 'gr/ml'"></span>)
                                        </label>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0.01" 
                                            :name="`resep[${index}][jumlah_pemakaian]`" 
                                            x-model.number="row.jumlah_pemakaian" 
                                            class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs font-bold focus:border-emerald-600 outline-none"
                                        >
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="w-full sm:w-28 text-right">
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Biaya Resep</label>
                                        <div class="text-xs font-bold text-slate-900 pt-1">
                                            Rp <span x-text="Math.round(calculateRowCost(row)).toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>

                                    <!-- Hapus -->
                                    <div class="pt-2 sm:pt-4">
                                        <button 
                                            type="button" 
                                            @click="removeRow(index)" 
                                            class="p-1.5 text-rose-500 hover:text-rose-700 font-bold text-xs"
                                            :disabled="resepRows.length <= 1"
                                        >
                                            ✕
                                        </button>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Live Simulator HPP & Keuntungan -->
                <div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs sticky top-20 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2.5">
                            📊 Kalkulasi Modal & Keuntungan
                        </h3>

                        <div class="space-y-2.5 text-xs border-b border-slate-100 pb-3">
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Bahan Baku:</span>
                                <span class="font-bold text-slate-900">Rp <span x-text="Math.round(totalCostBahan()).toLocaleString('id-ID')"></span></span>
                            </div>

                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Kemasan/Cup:</span>
                                <span class="font-bold text-slate-900">Rp <span x-text="(parseFloat(biayaLain) || 0).toLocaleString('id-ID')"></span></span>
                            </div>

                            <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-dashed border-slate-200">
                                <span>Total Modal (HPP):</span>
                                <span class="text-emerald-700 font-black">Rp <span x-text="Math.round(totalCostPerCup()).toLocaleString('id-ID')"></span></span>
                            </div>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Harga Jual:</span>
                                <span class="font-bold text-slate-900">Rp <span x-text="(parseFloat(hargaJual) || 0).toLocaleString('id-ID')"></span></span>
                            </div>

                            <div class="flex justify-between text-sm font-bold text-emerald-700">
                                <span>Keuntungan / Cup:</span>
                                <span class="font-black">Rp <span x-text="Math.round(keuntungan()).toLocaleString('id-ID')"></span></span>
                            </div>

                            <div class="flex justify-between items-center pt-1">
                                <span class="text-xs text-slate-600">Margin Profit:</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    <span x-text="marginPersen()"></span>%
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <button 
                                type="submit" 
                                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-all cursor-pointer"
                            >
                                Simpan Menu & Resep
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>
