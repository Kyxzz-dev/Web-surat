<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
      public function run(): void
    {
           Classification::insert([
            ['id' => 1, 'code' => 'PR 01', 'type' => 'Program dan Anggaran'],
            ['id' => 5, 'code' => 'PR 02', 'type' => 'Evaluasi'],
            ['id' => 6, 'code' => 'PR 03', 'type' => 'Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP)'],
        ]);
    }
}
