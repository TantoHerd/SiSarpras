<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'code'        => 'BOS',
                'name'        => 'Bantuan Operasional Sekolah',
                'description' => 'Dana BOS dari pemerintah pusat',
            ],
            [
                'code'        => 'BOPD',
                'name'        => 'Bantuan Operasional Pendidikan Daerah',
                'description' => 'Dana BOPD dari pemerintah daerah',
            ],
            [
                'code'        => 'BANPROV',
                'name'        => 'Bantuan Provinsi',
                'description' => 'Bantuan dari pemerintah provinsi',
            ],
            [
                'code'        => 'LAINNYA',
                'name'        => 'Lainnya',
                'description' => 'Sumber dana di luar kategori di atas',
            ],
        ];

        foreach ($sources as $source) {
            FundingSource::updateOrCreate(
                ['code' => $source['code']],
                array_merge($source, ['is_active' => true])
            );
        }
    }
}