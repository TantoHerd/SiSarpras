<?php
// database/seeders/SupplierSeeder.php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Elektronik Jaya',
                'phone' => '021-5555001',
                'email' => 'sales@elektronikjaya.com',
                'address' => 'Jl. Mangga Dua No. 15, Jakarta',
                'contact_person' => 'Bpk. Andi',
            ],
            [
                'name' => 'CV. Mebel Sejahtera',
                'phone' => '021-5555002',
                'email' => 'info@mebelsejahtera.com',
                'address' => 'Jl. Industri Raya No. 8, Tangerang',
                'contact_person' => 'Ibu Rina',
            ],
            [
                'name' => 'Toko ATK Makmur',
                'phone' => '021-5555003',
                'email' => 'atkmakmur@gmail.com',
                'address' => 'Jl. Pasar Baru No. 22, Jakarta',
                'contact_person' => 'Bpk. Hasan',
            ],
            [
                'name' => 'PT. Lab Science Indonesia',
                'phone' => '021-5555004',
                'email' => 'sales@labscience.co.id',
                'address' => 'Jl. Sudirman No. 45, Bandung',
                'contact_person' => 'Ibu Maya',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}