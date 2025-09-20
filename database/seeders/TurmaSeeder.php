<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Turma;

class TurmaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nome' => '1º Ano A', 'periodo' => 'manha'],
            ['nome' => '1º Ano B', 'periodo' => 'tarde'],
            ['nome' => '1º Ano C', 'periodo' => 'noite'],
        ];

        foreach ($data as $row) {
            Turma::firstOrCreate(
                ['nome' => $row['nome'], 'periodo' => $row['periodo']],
                []
            );
        }
    }
}

