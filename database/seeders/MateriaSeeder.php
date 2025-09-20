<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nome' => 'Matemática', 'quant_aulas' => 4, 'check' => true],
            ['nome' => 'Português',  'quant_aulas' => 4, 'check' => true],
            ['nome' => 'História',   'quant_aulas' => 2, 'check' => true],
        ];

        foreach ($data as $row) {
            Materia::updateOrCreate(['nome' => $row['nome']], $row);
        }
    }
}

